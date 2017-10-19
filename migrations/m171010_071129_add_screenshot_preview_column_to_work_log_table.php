<?php

use yii\db\Migration;

/**
 * Handles adding screenshot_preview to table `work_log`.
 */
class m171010_071129_add_screenshot_preview_column_to_work_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('work_log', 'screenshot_preview', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('work_log', 'screenshot_preview');
    }
}
