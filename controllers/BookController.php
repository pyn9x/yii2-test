<?php
namespace app\controllers;

use app\models\Book;
use app\models\search\BookSearch;
use app\models\Author;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/** @extends Controller<Module> */
class BookController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {
            $model->coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');

            if ($model->upload() && $model->validate()) {
                if ($model->save(false)) {
                    $model->saveAuthors($model->authorIds);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $this->getAuthorsList(),
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        $model->authorIds = $model->getAuthorIds();

        if ($model->load(Yii::$app->request->post())) {
            $model->coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');

            if ($model->upload() && $model->validate()) {
                if ($model->save(false)) {
                    $model->saveAuthors($model->authorIds);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $this->getAuthorsList(),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Book
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Book not found.');
    }

    /** @return array<int, string> */
    private function getAuthorsList(): array
    {
        return Author::find()->select('name')->indexBy('id')->column();
    }
}
