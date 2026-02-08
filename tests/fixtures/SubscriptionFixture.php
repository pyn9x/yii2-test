<?php
namespace app\tests\fixtures;

use app\models\Subscription;
use yii\test\ActiveFixture;

class SubscriptionFixture extends ActiveFixture
{
    public $modelClass = Subscription::class;
    public $dataFile = __DIR__ . '/data/subscription.php';
    public $depends = [
        UserFixture::class,
        AuthorFixture::class,
    ];
}

