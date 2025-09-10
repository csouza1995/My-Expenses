<?php

use app\models\Enums\ExpenseCategoriesEnum;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%expenses}}`.
 */
class m250909_052650_create_expenses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expenses}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'category' => $this->integer()->notNull(),
            'value' => $this->decimal(10, 2)->notNull(),
            'date' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%expenses}}');
    }
}
