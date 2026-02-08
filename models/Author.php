<?php
namespace app\models;

use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use Yii;
use app\components\SmsPilotEmulator;

/**
 * @property int $id
 * @property string $name
 * @property string|null $bio
 * @property int $created_at
 * @property int $updated_at
 * @property-read Book[] $books
 * @property-read Subscription[] $subscriptions
 * @property int|null $subscriptions_count
 * @property int|null $books_count
 */
class Author extends ActiveRecord
{
    public ?int $subscriptions_count = null;
    public ?int $books_count = null;

    public static function tableName(): string
    {
        return '{{%author}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            ['name', 'required'],
            [['name'], 'string', 'max' => 255],
            ['bio', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'bio' => 'Bio',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /** @return ActiveQuery<Book> */
    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['author_id' => 'id']);
    }

    /** @return ActiveQuery<Subscription> */
    public function getSubscriptions(): ActiveQuery
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id']);
    }

    /**
     * @param array<string, mixed> $changedAttributes
     * @throws InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            $message = sprintf('Автор %s обновлен', (string)$this->name);
            /** @var SmsPilotEmulator $sms */
            $sms = Yii::$app->get('sms');
            foreach ($this->getSubscriptions()->with('user')->all() as $subscription) {
                /** @var Subscription $subscription */
                if ($subscription->user instanceof User && $subscription->user->phone) {
                    $sms->send($subscription->user->phone, $message, 'author.update');
                }
            }
        }
    }
}
