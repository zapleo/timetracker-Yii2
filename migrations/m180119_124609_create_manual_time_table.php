<?php

use yii\db\Migration;

/**
 * Handles the creation of table `manual_time`.
 */
class m180119_124609_create_manual_time_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('manual_time', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'start_timestamp' => $this->bigInteger(),
            'end_timestamp' => $this->bigInteger(),
            'issue_key' => $this->string(),
            'comment' => $this->text(),
            'comment_admin' => $this->text(),
            'status' => $this->integer(1)->defaultValue(0),
            'created_at' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer()
        ]);

        $this->addForeignKey('manual_time_created_by_fk', 'manual_time', 'created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('manual_time_updated_by_fk', 'manual_time', 'updated_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('manual_time_user_id_fk', 'manual_time', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn('work_log', 'comment', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('manual_time_user_id_fk', 'manual_time');
        $this->dropForeignKey('manual_time_created_by_fk', 'manual_time');
        $this->dropForeignKey('manual_time_updated_by_fk', 'manual_time');
        $this->dropTable('manual_time');

        $this->dropColumn('work_log', 'comment');
    }
}
