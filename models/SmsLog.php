<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property string $recipient
 * @property string $message
 * @property string|null $context
 * @property string|null $status
 * @property string|null $entity_type
 * @property int|null $entity_id
 * @property int $created_at
 */
class SmsLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sms_log}}';
    }

    public function rules(): array
    {
        return [
            [['recipient', 'message', 'created_at'], 'required'],
            [['message'], 'string'],
            [['entity_id', 'created_at'], 'integer'],
            [['recipient', 'context', 'status', 'entity_type'], 'string', 'max' => 255],
        ];
    }

    public function behaviors(): array
    {
        return [[
            'class' => TimestampBehavior::class,
            'updatedAtAttribute' => false,
        ]];
    }
}
