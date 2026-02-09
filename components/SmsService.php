<?php
namespace app\components;

use app\models\SmsLog;
use Yii;

/**
 * Сервис для отправки SMS с поддержкой рейтлимита
 */
class SmsService
{
    private SmsPilotEmulator $smsPilot;

    // Максимум SMS на один номер в час
    private int $maxSmsPerHour = 3;

    // Максимум SMS на один номер в день
    private int $maxSmsPerDay = 10;

    public function __construct()
    {
        $this->smsPilot = new SmsPilotEmulator();
    }

    /**
     * Отправить SMS с проверкой рейтлимита
     */
    public function send(string $recipient, string $message, string $context = ''): array
    {
        // Проверяем рейтлимит
        $rateLimitCheck = $this->checkRateLimit($recipient);
        if (!$rateLimitCheck['allowed']) {
            return [
                'success' => false,
                'error' => $rateLimitCheck['message'],
            ];
        }

        // Отправляем SMS
        $success = $this->smsPilot->send($recipient, $message, $context);

        return [
            'success' => $success,
            'error' => $success ? null : 'Ошибка отправки SMS',
        ];
    }

    /**
     * Проверка рейтлимита для номера телефона
     */
    private function checkRateLimit(string $recipient): array
    {
        $now = time();
        $oneHourAgo = $now - 3600;
        $oneDayAgo = $now - 86400;

        // Проверяем количество SMS за последний час
        $countLastHour = SmsLog::find()
            ->where(['recipient' => $recipient])
            ->andWhere(['>=', 'created_at', $oneHourAgo])
            ->count();

        if ($countLastHour >= $this->maxSmsPerHour) {
            return [
                'allowed' => false,
                'message' => 'Превышен лимит отправки SMS. Максимум ' . $this->maxSmsPerHour . ' SMS в час.',
            ];
        }

        // Проверяем количество SMS за последний день
        $countLastDay = SmsLog::find()
            ->where(['recipient' => $recipient])
            ->andWhere(['>=', 'created_at', $oneDayAgo])
            ->count();

        if ($countLastDay >= $this->maxSmsPerDay) {
            return [
                'allowed' => false,
                'message' => 'Превышен лимит отправки SMS. Максимум ' . $this->maxSmsPerDay . ' SMS в день.',
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Получить информацию об оставшихся попытках
     */
    public function getRateLimitInfo(string $recipient): array
    {
        $now = time();
        $oneHourAgo = $now - 3600;
        $oneDayAgo = $now - 86400;

        $countLastHour = SmsLog::find()
            ->where(['recipient' => $recipient])
            ->andWhere(['>=', 'created_at', $oneHourAgo])
            ->count();

        $countLastDay = SmsLog::find()
            ->where(['recipient' => $recipient])
            ->andWhere(['>=', 'created_at', $oneDayAgo])
            ->count();

        return [
            'remainingPerHour' => max(0, $this->maxSmsPerHour - $countLastHour),
            'remainingPerDay' => max(0, $this->maxSmsPerDay - $countLastDay),
            'maxPerHour' => $this->maxSmsPerHour,
            'maxPerDay' => $this->maxSmsPerDay,
        ];
    }
}

