<?php

use yii\db\Migration;

class m260328_115248_create_url_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%url}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->string(2048)->notNull(),
            'short_code' => $this->string(10)->notNull()->unique(),
            'clicks' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%url_log}}', [
            'id' => $this->primaryKey(),
            'url_id' => $this->integer()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey('fk-url_log-url_id', '{{%url_log}}', 'url_id', '{{%url}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%url_log}}');
        $this->dropTable('{{%url}}');
    }
}
