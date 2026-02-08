<?php
namespace app\tests\fixtures;

use app\models\SmsLog;
use yii\test\ActiveFixture;

class SmsLogFixture extends ActiveFixture
{
    public $modelClass = SmsLog::class;
    public $dataFile = __DIR__ . '/data/sms_log.php';
}

