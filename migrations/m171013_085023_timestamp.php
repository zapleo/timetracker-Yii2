<?php

use yii\db\Migration;

class m171013_085023_timestamp extends Migration
{
    public function safeUp()
    {
        $this->addColumn('work_log','timestamp','BIGINT');
        $logs = \app\models\WorkLog::find()->all();
        foreach ($logs as $log)
        {
            $log->timestamp = (new DateTime($log->dateTime))->getTimestamp();
            $log->save();
        }
    }

    public function safeDown()
    {
        $this->
        $this->dropColumn('work_log','timestamp');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171013_085023_timestamp cannot be reverted.\n";

        return false;
    }
    */
}
