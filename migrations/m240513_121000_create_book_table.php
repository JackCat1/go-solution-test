<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m240513_121000_create_book_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'author' => $this->string()->notNull(),
            'description' => $this->text(),
            'year' => $this->integer(),
            'created_by' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-book-created_by',
            '{{%book}}',
            'created_by'
        );

        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                'created_by-book-fk',
                '{{%book}}',
                'created_by',
                '{{%user}}',
                'id',
                'CASCADE'
            );
        }
    }

    public function safeDown(): void
    {
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey('created_by-book-fk', '{{%book}}');
        }

        $this->dropIndex('idx-book-created_by', '{{%book}}');
        $this->dropTable('{{%book}}');
    }
}
