<?php
use yii\db\Migration;

class m260209_130000_add_guest_subscription_support extends Migration
{
    public function safeUp()
    {
        // Удаляем внешние ключи
        $this->dropForeignKey('fk_subscription_user', '{{%subscription}}');

        // Делаем user_id nullable для поддержки подписок гостей
        $this->alterColumn('{{%subscription}}', 'user_id', $this->integer()->null());

        // Добавляем поле phone для гостевых подписок
        $this->addColumn('{{%subscription}}', 'phone', $this->string(20)->null()->after('user_id'));

        // Удаляем старый уникальный индекс
        $this->dropIndex('idx_subscription_user_author', '{{%subscription}}');

        // Создаем новые индексы (не уникальные, так как могут быть NULL значения)
        $this->createIndex('idx_subscription_user_author', '{{%subscription}}', ['user_id', 'author_id']);
        $this->createIndex('idx_subscription_phone_author', '{{%subscription}}', ['phone', 'author_id']);

        // Восстанавливаем внешний ключ
        $this->addForeignKey('fk_subscription_user', '{{%subscription}}', 'user_id', '{{%user}}', 'id', 'CASCADE');

        // Добавляем проверку: должен быть указан либо user_id, либо phone
        $this->execute('ALTER TABLE {{%subscription}} ADD CONSTRAINT chk_subscription_user_or_phone CHECK (user_id IS NOT NULL OR phone IS NOT NULL)');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE {{%subscription}} DROP CONSTRAINT chk_subscription_user_or_phone');
        $this->dropForeignKey('fk_subscription_user', '{{%subscription}}');
        $this->dropIndex('idx_subscription_phone_author', '{{%subscription}}');
        $this->dropIndex('idx_subscription_user_author', '{{%subscription}}');
        $this->dropColumn('{{%subscription}}', 'phone');
        $this->alterColumn('{{%subscription}}', 'user_id', $this->integer()->notNull());
        $this->createIndex('idx_subscription_user_author', '{{%subscription}}', ['user_id', 'author_id'], true);
        $this->addForeignKey('fk_subscription_user', '{{%subscription}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }
}

