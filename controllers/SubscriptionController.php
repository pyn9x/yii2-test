<?php
namespace app\controllers;

use app\models\Author;
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
                'only' => ['index', 'subscribe', 'unsubscribe'],
                'rules' => [
                    [
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

    public function actionSubscribe(int $authorId): Response
    {
        $author = Author::findOne($authorId);
        if (!$author) {
            throw new NotFoundHttpException('Author not found');
        }

        $model = Subscription::findOne(['user_id' => Yii::$app->user->id, 'author_id' => $authorId]);
        if (!$model) {
            $model = new Subscription([
                'user_id' => Yii::$app->user->id,
                'author_id' => $authorId,
            ]);
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', 'Не удалось подписаться');
            } else {
                Yii::$app->session->setFlash('success', 'Подписка оформлена');
            }
        }

        return $this->redirect(['author/view', 'id' => $authorId]);
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
