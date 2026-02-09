<?php

use yii\db\Migration;

/**
 * Изменение структуры таблицы book:
 * - Добавление полей isbn и cover_image
 * - Переход на many-to-many связь книга-автор через промежуточную таблицу book_author
 */
class m260209_120000_update_book_structure extends Migration
{
    public function safeUp()
    {
        // Создаем промежуточную таблицу для many-to-many связи
        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'PRIMARY KEY(book_id, author_id)',
        ]);

        // Мигрируем существующие данные из book.author_id в book_author
        $this->execute('INSERT INTO {{%book_author}} (book_id, author_id) SELECT id, author_id FROM {{%book}} WHERE author_id IS NOT NULL');

        // Добавляем индексы и внешние ключи для book_author
        $this->createIndex('idx_book_author_book', '{{%book_author}}', 'book_id');
        $this->createIndex('idx_book_author_author', '{{%book_author}}', 'author_id');
        $this->addForeignKey('fk_book_author_book', '{{%book_author}}', 'book_id', '{{%book}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_book_author_author', '{{%book_author}}', 'author_id', '{{%author}}', 'id', 'CASCADE');

        // Удаляем старый внешний ключ и индекс
        $this->dropForeignKey('fk_book_author', '{{%book}}');
        $this->dropIndex('idx_book_author', '{{%book}}');

        // Удаляем колонку author_id из book
        $this->dropColumn('{{%book}}', 'author_id');

        // Добавляем новые поля в таблицу book
        $this->addColumn('{{%book}}', 'isbn', $this->string(17)->unique()->after('title'));
        $this->addColumn('{{%book}}', 'cover_image', $this->string(255)->null()->after('description'));
    }

    public function safeDown()
    {
        // Удаляем новые поля
        $this->dropColumn('{{%book}}', 'cover_image');
        $this->dropColumn('{{%book}}', 'isbn');

        // Восстанавливаем колонку author_id
        $this->addColumn('{{%book}}', 'author_id', $this->integer()->null()->after('id'));

        // Мигрируем данные обратно (берем первого автора из связки)
        $this->execute('UPDATE {{%book}} b SET author_id = (SELECT author_id FROM {{%book_author}} WHERE book_id = b.id LIMIT 1)');

        // Восстанавливаем индекс и внешний ключ
        $this->createIndex('idx_book_author', '{{%book}}', 'author_id');
        $this->addForeignKey('fk_book_author', '{{%book}}', 'author_id', '{{%author}}', 'id', 'CASCADE');

        // Удаляем промежуточную таблицу
        $this->dropTable('{{%book_author}}');
    }
}

