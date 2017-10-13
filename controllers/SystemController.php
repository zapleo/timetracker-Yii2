<?php
/**
 * Created by PhpStorm.
 * User: zapleo
 * Date: 10.10.17
 * Time: 10:34
 */

namespace app\controllers;

use app\components\helpers\SimpleImage;
use app\controllers\base\BaseController;
use app\models\User;
use app\models\WorkLog;
use GuzzleHttp\Client;
use yii\db\Query;
use yii\web\HttpException;

class SystemController extends BaseController
{

    /**
     *
     */
    public function actionIndex()
    {
        echo 'System Controller';
    }

    /**
     * @param bool $end
     * @return array|bool|mixed|string
     */
    private function getDate($end = false)
    {

        $timeStart = \Yii::$app->request->post('timeStart',false);
        $timeEnd = \Yii::$app->request->post('timeEnd',false);

        $timeStart = is_null($timeStart) ? false :$timeStart;
        $timeEnd = is_null($timeEnd) ? false :$timeEnd;

        if (!$end) {

            $timeStart = \DateTime::createFromFormat('d/m/Y H:i:s', $timeStart)->format('Y-m-d 00:00:00');

            return $timeStart;

        } else {

            if (!$timeEnd) {
                $timeEnd = \DateTime::createFromFormat('d/m/Y H:i:s', $timeStart)->format('Y-m-d 23:59:00');
            } else {
                $timeEnd = \DateTime::createFromFormat('d/m/Y H:i:s', $timeEnd)->format('Y-m-d 23:59:00');
            }

            return $timeEnd;

        }
    }


    private function generatePreview($item)
    {
        $path = \Yii::$app->basePath."/web";
        $preview_name = $path.'/preview_screenshots/' . date('Y/m/d').'/'.$item['id'].'.jpg';
        if (file_exists($path.'/screenshots/' . $item['screenshot'])) {

            if (!is_dir($path.'/preview_screenshots/' . date('Y/m/d')))
                mkdir($path.'/preview_screenshots/' . date('Y/m/d'), 0777, true);

            $image = new SimpleImage();
            $image->load($path.'/screenshots/' . $item['screenshot']);
            $image->resizeToHeight(156);

            if ($image->getWidth() > 280)
                $image->crop(280, 156);

            $item['preview_screenshots'] = date('Y/m/d').'/'.$item['id'].'.jpg';
            $image->save($preview_name);

            imagedestroy($image);

            if (file_exists($preview_name)) {
                return $item['preview_screenshots'];
            }

        }
        return false;
    }

    /**
     * @return array
     */
    public function actionGetUsersList()
    {
        $user  = User::findOne(\Yii::$app->user->id);
        $data = [];
        if($user->isAdmin())
        {
            $users = User::find()->all();
        }
        else
            $users[] = $user;

        foreach ($users as $u)
        {
            $data[] = ['id'=> $u->id,'last_name'=>$u->last_name,'first_name'=>$u->first_name];
        }

        $this->formatJson();
        return $data;
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionGetUserInfo($id)
    {
        if($id == \Yii::$app->user->id || \Yii::$app->user->identity->isAdmin())
        {

            $info = User::findOne($id);
            if(is_null($info))
                throw new HttpException(404);
            $this->formatJson();
            return $info->toArray();
        }
        throw new HttpException(403);
    }

    /**
     * @return array|int
     */
    public function actionGetProjects()
    {
        $uid = \Yii::$app->request->post('uid',false);

        if(!\Yii::$app->user->identity->isAdmin())
            $uid = array(\Yii::$app->user->id);

        if ($uid) {


            $timeStart = \Yii::$app->request->post('dt_start',false);
            $timeEnd =  \Yii::$app->request->post('dt_end',false);

            // Select user projects
            $in  = implode(',',$uid);
            $query = new Query();
            $data = $query->select(["DISTINCT SUBSTRING_INDEX(issueKey, '-', 1 ) AS project"])
                ->from('work_log')->where('user_id IN ('.$in.')')
                ->andWhere('dateTime BETWEEN "'.$timeStart.'" AND "'.$timeEnd.'"')->orderBy('project')
                ->all();

            $this->formatJson();

            return $data;
        }

        return null;
    }

    /**
     * @return array|int
     */
    public function actionGetTasks()
    {
        $uid = \Yii::$app->request->post('uid',false);
        $project = \Yii::$app->request->post('project',false);

        if(!\Yii::$app->user->identity->isAdmin())
            $uid = array(\Yii::$app->user->id);

        if ($project) {

            $timeStart = \Yii::$app->request->post('dt_start',false);
            $timeEnd =  \Yii::$app->request->post('dt_end',false);

            $in  = implode(',',$uid);
            $query = new Query();
            $data = $query->select(["DISTINCT `issueKey` AS task"])
                ->from('work_log')->where('user_id IN ('.$in.')')
                ->andWhere('dateTime BETWEEN "'.$timeStart.'" AND "'.$timeEnd.'"')
                ->andWhere('issueKey LIKE :project')->params(['project'=>$project.'%'])
                ->orderBy('task')
                ->all();

            $this->formatJson();
            return $data;
        }

        return null;
    }

    /**
     * @param $user_id
     * @return array|\DateTime
     */
    public function actionGetFullLogs($user_id)
    {
        $project = \Yii::$app->request->post('project',false);
        $task = \Yii::$app->request->post('task',false);
        $type = \Yii::$app->request->post('type',false);

        if (\Yii::$app->user->id != $user_id && \Yii::$app->user->identity->isAdmin()) {
            return null;
        }

        $timeStart = \Yii::$app->request->post('dt_start',false);
        $timeEnd = \Yii::$app->request->post('dt_end',false);

        if(isset($timeStart) && isset($timeEnd)) {
            $query = new Query();
            $query->addSelect(['SUBSTRING_INDEX(GROUP_CONCAT(CAST(id AS CHAR) ORDER BY timestamp DESC), \',\', 1 ) as id,
             SUBSTRING_INDEX(GROUP_CONCAT(CAST(user_id AS CHAR) ORDER BY timestamp DESC), \',\', 1 ) as user_id,
             SUBSTRING_INDEX(GROUP_CONCAT(CAST(screenshot AS CHAR) ORDER BY timestamp DESC), \',\', 1 ) as screenshot,
             SUBSTRING_INDEX(GROUP_CONCAT(CAST(screenshot_preview AS CHAR) ORDER BY timestamp DESC), \',\', 1 ) as screenshot_preview,
             COUNT(id) as count, SUM(activityIndex) as ai,
             MIN(timestamp) as tstart, MAX(timestamp) as tend,
             SUM(CASE WHEN workTime = 1 THEN 1 ELSE 0 END) work_count,
             SUM(CASE WHEN workTime = 0 THEN 1 ELSE 0 END) no_work_count']);
            if ($type == 'hour')
                $query->groupBy([' DATE_FORMAT(FROM_UNIXTIME(timestamp), \'%y%m%d%H\')']);
            elseif ($type == 'day')
                $query->groupBy([' DATE_FORMAT(FROM_UNIXTIME(timestamp), \'%y%m%d\')']);
            elseif($type == 'month')
                $query->groupBy([' DATE_FORMAT(FROM_UNIXTIME(timestamp), \'%y%m\')']);
            else
                $query->groupBy(['timestamp']);

            $query->from('work_log');

            $query->where(['user_id' => $user_id])
                ->andWhere('timestamp BETWEEN :start AND :end', ['start' => $timeStart, 'end' => $timeEnd]);

            if ($project)
                $query->andWhere(['issueKey LIKE :project'], ['project' => $project . '%']);

            if ($task)
                $query->andWhere(['issueKey LIKE :task'], ['task' => $task]);
            $query->orderBy('tend DESC');
            $data = $query->all();

//            foreach ($data as $k=>$item)
//            {
//                if($item['count']>1 || is_null($item['screenshot_preview']))
//                {
//                    $log = WorkLog::findOne($item['id']);
//                    if(!is_null($log))
//                        if(is_null($log->screenshot_preview))
//                        {
//                            $img = $this->generatePreview($item);
//                            if($img)
//                            {
//                                $log->screenshot_preview = $img;
//                                $log->save();
//                                $data[$k]['screenshot_preview']=$img;
//                            }
//
//                        }
//
//                }
//
//            }
            $this->formatJson();
            return $data;
        }

        return null;

    }

    public function actionGetWorkLogById($id)
    {
        $this->formatJson();
        if(\Yii::$app->user->identity->isAdmin())
            $data = WorkLog::findOne(['id'=>$id]);
        else
            $data = WorkLog::findOne(['id'=>$id,'user_id'=>\Yii::$app->user->id]);
        $this->formatJson();

        return is_null($data)?$data:$data->toArray();
    }

}