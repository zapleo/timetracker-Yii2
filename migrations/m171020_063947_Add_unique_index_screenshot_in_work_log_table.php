<?php

use yii\db\Migration;

class m171020_063947_Add_unique_index_screenshot_in_work_log_table extends Migration
{
    public function safeUp()
    {
        $this->createIndex('screenshot_index', 'work_log', 'screenshot', true);
    }

    public function safeDown()
    {
        $this->dropIndex('screenshot_index', 'work_log');
    }
}
