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
 * @property int $author_id
 * @property string $title
 * @property string|null $description
 * @property string|null $published_at
 * @property int $created_at
 * @property int $updated_at
 * @property-read Author|null $author
 */
class Book extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['author_id', 'title'], 'required'],
            ['author_id', 'integer'],
            ['title', 'string', 'max' => 255],
            [['description'], 'string'],
            ['published_at', 'date', 'format' => 'php:Y-m-d'],
            ['author_id', 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author',
            'title' => 'Title',
            'description' => 'Description',
            'published_at' => 'Published At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /** @return ActiveQuery<Author> */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * @param array<string, mixed> $changedAttributes
     * @throws InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $author = $this->author;
            $message = sprintf('Новая книга "%s" автора %s', (string)$this->title, $author ? $author->name : '');
            if ($author) {
                /** @var SmsPilotEmulator $sms */
                $sms = Yii::$app->get('sms');
                foreach ($author->getSubscriptions()->with('user')->all() as $subscription) {
                    /** @var Subscription $subscription */
                    if ($subscription->user instanceof User && $subscription->user->phone) {
                        $sms->send($subscription->user->phone, $message, 'book.create');
                    }
                }
            }
        }
    }
}
