<?php
namespace app\components;

use app\models\SmsLog;
use Yii;

class SmsPilotEmulator
{
    public function send(string $recipient, string $message, string $context = ''): bool
    {
        $log = new SmsLog();
        $log->recipient = $recipient;
        $log->message = $message;
        $log->context = $context;
        $log->status = 'sent';
        $log->created_at = time();
        return $log->save(false);
    }
}

