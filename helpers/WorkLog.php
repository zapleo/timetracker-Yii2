<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: WorkLog.php
 * Date: 25.01.18
 * Time: 9:03
 */

namespace app\helpers;

use app\components\Auth;
use app\models\ManualTime;
use app\models\WorkLog as WorkLogModel;

class WorkLog
{
    /**
     * @param \app\models\ManualTime $model
     *
     * @throws \yii\base\Exception
     */
    public function addManualTime(ManualTime $model)
    {
        for ($start = $model->start_timestamp; $start <= $model->end_timestamp; $start += 600) {
            $log = WorkLogModel::findOne(['user_id' => $model->user_id, 'timestamp' => $start]);

            if (!is_null($log))
                continue;

            $work_log = new WorkLogModel();
            $work_log->user_id = $model->user_id;
            $work_log->timestamp = $start;
            $work_log->activityIndex = 66;
            $work_log->issueKey = $model->issue_key;
            $work_log->workTime = WorkTime::check($start);
            $work_log->dateTime = date('Y-m-d H:i:s', $start);
            $work_log->comment = (!empty($model->comment) ? $model->comment : 'Manual time');

            $work_log->screenshot = time().\Yii::$app->security->generateRandomString(6);
            $work_log->countMouseEvent = 0;
            $work_log->countKeyboardEvent = 0;

            $work_log->manual_time_id = $model->id;

            if ($work_log->save()) {
                $token = (new Auth())->getToken();
                $jlog = (new JiraApiHelper($token))->addWorkLog($model->issue_key, $start, $model->comment);

                $work_log->work_log_id = $jlog['id'];
                $work_log->save();
            }
        }
    }

    /**
     * @param $manual_time_id
     *
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteManualTime($manual_time_id)
    {
        $logs = WorkLogModel::findAll(['manual_time_id' => $manual_time_id]);

        foreach ($logs as $log) {
            $token = (new Auth())->getToken();
            (new JiraApiHelper($token))->deleteWorkLog($log->issueKey, $log->work_log_id);

            $log->delete();
        }
    }
}