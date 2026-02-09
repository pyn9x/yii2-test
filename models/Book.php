<?php
namespace app\models;

use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;
use Yii;
use app\components\SmsPilotEmulator;

/**
 * @property int $id
 * @property string $title
 * @property string|null $isbn
 * @property string|null $description
 * @property string|null $cover_image
 * @property string|null $published_at
 * @property int $created_at
 * @property int $updated_at
 * @property-read Author[] $authors
 */
class Book extends ActiveRecord
{
    /** @var array<int> */
    public array $authorIds = [];

    public ?UploadedFile $coverImageFile = null;

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
            [['title'], 'required'],
            ['title', 'string', 'max' => 255],
            [['description'], 'string'],
            ['published_at', 'date', 'format' => 'php:Y-m-d'],
            ['isbn', 'string', 'max' => 17],
            ['isbn', 'unique'],
            ['isbn', 'match', 'pattern' => '/^(?:ISBN(?:-1[03])?:?\s?)?(?=[0-9X]{10}$|(?=(?:[0-9]+[-\s]){3})[-\s0-9X]{13}$|97[89][0-9]{10}$|(?=(?:[0-9]+[-\s]){4})[-\s0-9]{17}$)(?:97[89][-\s]?)?[0-9]{1,5}[-\s]?[0-9]+[-\s]?[0-9]+[-\s]?[0-9X]$/i', 'message' => 'Invalid ISBN format'],
            ['authorIds', 'required'],
            ['authorIds', 'each', 'rule' => ['integer']],
            ['authorIds', 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => 'id']],
            ['coverImageFile', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        // Exclude coverImageFile from mass assignment
        $scenarios[self::SCENARIO_DEFAULT] = array_diff($scenarios[self::SCENARIO_DEFAULT], ['coverImageFile']);
        return $scenarios;
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'isbn' => 'ISBN',
            'description' => 'Description',
            'cover_image' => 'Cover Image',
            'coverImageFile' => 'Cover Image',
            'published_at' => 'Published At',
            'authorIds' => 'Authors',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /** @return ActiveQuery<Author> */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /** @return array<int> */
    public function getAuthorIds(): array
    {
        if (!empty($this->authorIds)) {
            return $this->authorIds;
        }
        return $this->getAuthors()->select('id')->column();
    }

    /** @param array<int> $authorIds */
    public function saveAuthors(array $authorIds): bool
    {
        // Удаляем старые связи
        Yii::$app->db->createCommand()
            ->delete('{{%book_author}}', ['book_id' => $this->id])
            ->execute();

        // Добавляем новые связи
        foreach ($authorIds as $authorId) {
            Yii::$app->db->createCommand()
                ->insert('{{%book_author}}', [
                    'book_id' => $this->id,
                    'author_id' => $authorId,
                ])
                ->execute();
        }

        return true;
    }

    public function upload(): bool
    {
        if ($this->coverImageFile === null) {
            return true;
        }

        $uploadPath = Yii::getAlias('@webroot/uploads/covers');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = uniqid('book_', true) . '.' . $this->coverImageFile->extension;
        $filepath = $uploadPath . '/' . $filename;

        if ($this->coverImageFile->saveAs($filepath)) {
            // Удаляем старое изображение если есть
            if ($this->cover_image && file_exists($uploadPath . '/' . $this->cover_image)) {
                @unlink($uploadPath . '/' . $this->cover_image);
            }
            $this->cover_image = $filename;
            return true;
        }

        return false;
    }

    /**
     * @param array<string, mixed> $changedAttributes
     * @throws InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $authors = $this->authors;
            $authorNames = array_map(fn($author) => $author->name, $authors);
            $message = sprintf('Новая книга "%s" автора %s', (string)$this->title, implode(', ', $authorNames));

            /** @var SmsPilotEmulator $sms */
            $sms = Yii::$app->get('sms');

            foreach ($authors as $author) {
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
