<?php

use yii\db\Migration;

class m180125_071219_update_work_log_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('work_log', 'work_log_id', $this->integer()->defaultValue(NULL));
        $this->addColumn('work_log', 'manual_time_id', $this->integer()->defaultValue(NULL));

        $this->addForeignKey('work_log_manual_time_id_fk', 'work_log', 'manual_time_id', 'manual_time', 'id', 'CASCADE', 'CASCADE');

    }

    public function safeDown()
    {
        $this->dropForeignKey('work_log_manual_time_id_fk', 'work_log');

        $this->dropColumn('work_log', 'manual_time_id');
        $this->dropColumn('work_log', 'work_log_id');
    }
}
