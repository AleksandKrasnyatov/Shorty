<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%links}}`.
 */
class m250822_123940_create_links_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%links}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->text()->notNull(),
            'short_code' => $this->string(8)->notNull()->unique(),
            'clicks' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-links-short_code', 'links', 'short_code');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%links}}');
    }
}
