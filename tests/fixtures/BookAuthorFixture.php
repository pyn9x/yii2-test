<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class BookAuthorFixture extends ActiveFixture
{
    public $tableName = '{{%book_author}}';
    public $dataFile = __DIR__ . '/data/book_author.php';
    public $depends = [
        BookFixture::class,
        AuthorFixture::class,
    ];
}

