<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $phone
 * @property int $author_id
 * @property int $created_at
 * @property-read User|null $user
 * @property-read Author $author
 */
class Subscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%subscription}}';
    }

    public function behaviors(): array
    {
        return [[
            'class' => TimestampBehavior::class,
            'updatedAtAttribute' => false,
        ]];
    }

    public function rules(): array
    {
        return [
            [['author_id'], 'required'],
            [['user_id', 'author_id'], 'integer'],
            ['phone', 'string', 'max' => 20],
            // Проверка: должен быть указан либо user_id, либо phone
            ['user_id', 'required', 'when' => function($model) {
                return empty($model->phone);
            }, 'message' => 'Необходимо указать пользователя или номер телефона'],
            ['phone', 'required', 'when' => function($model) {
                return empty($model->user_id);
            }, 'message' => 'Необходимо указать номер телефона или пользователя'],
            // Уникальность для пользователей
            [['user_id', 'author_id'], 'unique', 'targetAttribute' => ['user_id', 'author_id'],
                'when' => function($model) { return !empty($model->user_id); },
                'message' => 'Вы уже подписаны на этого автора'],
            // Уникальность для гостей
            [['phone', 'author_id'], 'unique', 'targetAttribute' => ['phone', 'author_id'],
                'when' => function($model) { return !empty($model->phone); },
                'message' => 'Этот номер телефона уже подписан на этого автора'],
            ['user_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            ['author_id', 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'phone' => 'Phone',
            'author_id' => 'Author',
            'created_at' => 'Created At',
        ];
    }

    /** @return ActiveQuery<User> */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /** @return ActiveQuery<Author> */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}
