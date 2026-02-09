<?php
namespace app\controllers;

use app\components\SmsService;
use app\models\Author;
use app\models\GuestSubscriptionForm;
use app\models\Subscription;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/** @extends Controller<Module> */
class SubscriptionController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['subscribe'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['index', 'unsubscribe'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'unsubscribe' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $subscriptions = Subscription::find()
            ->with(['author'])
            ->where(['user_id' => Yii::$app->user->id])
            ->all();

        return $this->render('index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    public function actionSubscribe(int $authorId): Response|string
    {
        $author = Author::findOne($authorId);
        if (!$author) {
            throw new NotFoundHttpException('Author not found');
        }

        // Если пользователь авторизован
        if (!Yii::$app->user->isGuest) {
            $subscription = Subscription::findOne(['user_id' => Yii::$app->user->id, 'author_id' => $authorId]);
            if ($subscription) {
                Yii::$app->session->setFlash('info', 'Вы уже подписаны на этого автора');
                return $this->redirect(['author/view', 'id' => $authorId]);
            }

            $model = new Subscription([
                'user_id' => Yii::$app->user->id,
                'author_id' => $authorId,
            ]);

            if ($model->save()) {
                // Отправляем СМС авторизованному пользователю
                $user = Yii::$app->user->identity;
                $smsService = new SmsService();
                $result = $smsService->send(
                    $user->phone,
                    "Вы подписались на обновления автора: {$author->name}",
                    'subscription'
                );

                if (!$result['success']) {
                    Yii::$app->session->setFlash('warning', 'Подписка оформлена, но SMS не отправлено: ' . $result['error']);
                } else {
                    Yii::$app->session->setFlash('success', 'Подписка оформлена! SMS-уведомление отправлено.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось подписаться');
            }

            return $this->redirect(['author/view', 'id' => $authorId]);
        }

        // Если пользователь гость - показываем форму с номером телефона
        $model = new GuestSubscriptionForm();
        $model->author_id = $authorId;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Создаем подписку для гостя
            $subscription = new Subscription([
                'phone' => $model->phone,
                'author_id' => $authorId,
            ]);

            if ($subscription->save()) {
                // Отправляем СМС с рейтлимитом
                $smsService = new SmsService();
                $result = $smsService->send(
                    $model->phone,
                    "Вы подписались на обновления автора: {$author->name}",
                    'guest_subscription'
                );

                if (!$result['success']) {
                    Yii::$app->session->setFlash('warning', 'Подписка оформлена, но SMS не отправлено: ' . $result['error']);
                } else {
                    Yii::$app->session->setFlash('success', 'Подписка оформлена! SMS-уведомление отправлено.');
                }

                return $this->redirect(['author/view', 'id' => $authorId]);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось подписаться');
            }
        }

        return $this->render('subscribe-guest', [
            'model' => $model,
            'author' => $author,
        ]);
    }

    public function actionUnsubscribe(int $id): Response
    {
        $model = Subscription::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($model) {
            $authorId = $model->author_id;
            $model->delete();
            return $this->redirect(['author/view', 'id' => $authorId]);
        }
        throw new NotFoundHttpException('Subscription not found');
    }
}
