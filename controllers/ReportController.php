<?php
namespace app\controllers;

use app\models\Author;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\db\Expression;

/** @extends Controller<Module> */
class ReportController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['top-authors'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionTopAuthors(): string
    {
        $authors = Author::find()
            ->alias('a')
            ->select([
                'a.*',
                'subscriptions_count' => new Expression('COUNT(DISTINCT s.id)'),
                'books_count' => new Expression('COUNT(DISTINCT b.id)'),
            ])
            ->leftJoin('{{%subscription}} s', 's.author_id = a.id')
            ->leftJoin('{{%book}} b', 'b.author_id = a.id')
            ->groupBy('a.id')
            ->orderBy(['subscriptions_count' => SORT_DESC, 'books_count' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('top-authors', [
            'authors' => $authors,
        ]);
    }
}
