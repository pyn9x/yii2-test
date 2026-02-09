<?php
namespace app\models;

use yii\base\Model;

/**
 * Форма подписки для гостей
 */
class GuestSubscriptionForm extends Model
{
    public ?int $author_id = null;
    public ?string $phone = null;

    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            ['author_id', 'integer'],
            ['author_id', 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+?[0-9]{10,15}$/', 'message' => 'Неверный формат номера телефона. Используйте формат: +70000000000'],
            ['phone', 'validateUniqueSubscription'],
        ];
    }

    public function validateUniqueSubscription($attribute): void
    {
        if (!$this->hasErrors()) {
            $subscription = Subscription::find()
                ->where(['phone' => $this->phone, 'author_id' => $this->author_id])
                ->one();

            if ($subscription) {
                $this->addError($attribute, 'Вы уже подписаны на этого автора.');
            }
        }
    }

    public function attributeLabels(): array
    {
        return [
            'author_id' => 'Автор',
            'phone' => 'Номер телефона',
        ];
    }
}

