<?php
use Yii;
use yii\db\Migration;

class m240208_000001_init_schema extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string(32)->notNull(),
            'role' => $this->string(32)->notNull()->defaultValue('user'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'access_token' => $this->string(64),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'bio' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'published_at' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%sms_log}}', [
            'id' => $this->primaryKey(),
            'recipient' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'context' => $this->string()->defaultValue(''),
            'status' => $this->string(16)->defaultValue('sent'),
            'entity_type' => $this->string(32),
            'entity_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_book_author', '{{%book}}', 'author_id');
        $this->addForeignKey('fk_book_author', '{{%book}}', 'author_id', '{{%author}}', 'id', 'CASCADE');

        $this->createIndex('idx_subscription_user_author', '{{%subscription}}', ['user_id', 'author_id'], true);
        $this->addForeignKey('fk_subscription_user', '{{%subscription}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_subscription_author', '{{%subscription}}', 'author_id', '{{%author}}', 'id', 'CASCADE');

        $this->batchInsert('{{%author}}', ['name', 'bio', 'created_at', 'updated_at'], [
            ['Аркадий Стругацкий', 'Фантастика.', time(), time()],
            ['Борис Стругацкий', 'Фантастика.', time(), time()],
            ['Курт Воннегут', 'Сатира и фантастика.', time(), time()],
        ]);

        $passwordHash = Yii::$app->security->generatePasswordHash('user123');
        $this->insert('{{%user}}', [
            'username' => 'user',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => $passwordHash,
            'email' => 'user@example.com',
            'phone' => '+70000000000',
            'role' => 'user',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sms_log}}');
        $this->dropTable('{{%subscription}}');
        $this->dropTable('{{%book}}');
        $this->dropTable('{{%author}}');
        $this->dropTable('{{%user}}');
    }
}

