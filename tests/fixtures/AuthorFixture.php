<?php
namespace app\tests\fixtures;

use app\models\Author;
use yii\test\ActiveFixture;

class AuthorFixture extends ActiveFixture
{
    public $modelClass = Author::class;
    public $dataFile = __DIR__ . '/data/author.php';
}

