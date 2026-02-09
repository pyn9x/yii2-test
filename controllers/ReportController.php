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

    public function actionTopAuthors(int $year = null): string
    {
        if ($year === null) {
            $year = (int)date('Y');
        }

        $authors = Author::find()
            ->alias('a')
            ->select([
                'a.*',
                'books_count' => new Expression('COUNT(DISTINCT b.id)'),
            ])
            ->innerJoin('{{%book_author}} ba', 'ba.author_id = a.id')
            ->innerJoin('{{%book}} b', 'b.id = ba.book_id AND YEAR(b.published_at) = :year', [':year' => $year])
            ->groupBy('a.id')
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('top-authors', [
            'authors' => $authors,
            'year' => $year,
        ]);
    }
}
