<?php
/**
 * Created by PhpStorm.
 * User: zapleo
 * Date: 10.10.17
 * Time: 10:34
 */

namespace app\controllers;


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
    public function actionGetDate($end = false)
    {

        $timeStart = \Yii::$app->request->post('timeStart',false);
        $timeEnd = \Yii::$app->request->post('timeEnd',false);

        $timeStart = is_null($timeStart) ? false :$timeStart;
        $timeEnd = is_null($timeEnd) ? false :$timeEnd;

        if (!$end) {

            $timeStart = \DateTime::createFromFormat('d/m/Y', $timeStart)->format('Y-m-d 00:00:00');

            return $timeStart;

        } else {

            if (!$timeEnd) {
                $timeEnd = \DateTime::createFromFormat('d/m/Y', $timeStart)->format('Y-m-d 23:59:00');
                //$timeEnd = date('Y-m-d', strtotime($timeEnd.'+1 day'));
            } else {
                $timeEnd = \DateTime::createFromFormat('d/m/Y', $timeEnd)->format('Y-m-d 23:59:00');
                //$timeEnd = date('Y-m-d', strtotime($timeEnd.'+1 day'));
            }

            return $timeEnd;

        }
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
        $month = \Yii::$app->request->post('month',false);

        if(!\Yii::$app->user->identity->isAdmin())
            $uid = array(\Yii::$app->user->id);

        if ($uid) {

            if (!$month) {
                $timeStart = $this->actionGetDate();
                $timeEnd = $this->actionGetDate(1);
            } else {
                $date = new \DateTime();
                $timeStart = $date->format('Y-'.$month.'-01 00:00:00');
                $timeEnd = $date->format('Y-'.$month.'-31 23:59:00');
            }

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

        return 0;
    }

    /**
     * @return array
     */
    public function actionGetTasks()
    {
        $uid = \Yii::$app->request->post('uid',false);
        $project = \Yii::$app->request->post('project',false);
        $month = \Yii::$app->request->post('month',false);

        if(!\Yii::$app->user->identity->isAdmin())
            $uid = array(\Yii::$app->user->id);

        if ($project) {

            if (!$month) {
                $timeStart = $this->actionGetDate();
                $timeEnd = $this->actionGetDate(1);
            } else {
                $date = new \DateTime();
                $timeStart = $date->format('Y-'.$month.'-01 00:00:00');
                $timeEnd = $date->format('Y-'.$month.'-31 23:59:00');
            }

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
    }

    public function actionGetFullLogs($user_id)
    {
        $project = \Yii::$app->request->post('project',false);
        $task = \Yii::$app->request->post('task',false);
        $type = \Yii::$app->request->post('type',false);
        $month = \Yii::$app->request->post('month',false);


        if (\Yii::$app->user->id != $user_id && \Yii::$app->user->identity->isAdmin()) {
            exit;
        }

        if (!$type) {

            $timeStart = $this->actionGetDate();
            $timeEnd = $this->actionGetDate(1);

        } elseif ($type == 'day') {

            $date = new \DateTime();
            $timeStart = $date->format('Y-'.$month.'-01 00:00:00');
            $timeEnd = $date->format('Y-'.$month.'-31 23:59:00');
        }

        if(isset($timeStart) && isset($timeEnd))
        {

            $query = new Query();

            if(!$type)
                $query->select(['LAST_VALUE(id) OVER (PARTITION BY DATE_FORMAT(dateTime, \'%y%m%d%H\') ORDER BY dateTime DESC)']);
            else
                $query->select(['LAST_VALUE(id) OVER (PARTITION BY DATE_FORMAT(dateTime, \'%y%m%d\') ORDER BY dateTime DESC)']);

            $query->addSelect(['id, user_id, COUNT(id) as count, SUM(activityIndex) as ai,
                        MIN(dateTime) as tstart, MAX(dateTime) as tend,screenshot,screenshot_preview']);
            $query->from('work_log');

            $query->where(['user_id' => $user_id])
                ->andWhere('dateTime BETWEEN :start AND :end',['start'=>$timeStart,'end'=>$timeEnd]);

            if($project)
                $query->andWhere(['issueKey LIKE :project'],['project'=>$project.'%']);

            if($project)
                $query->andWhere(['issueKey LIKE :task'],['task'=>$task]);

            $date = $query->all();
            $this->formatJson();
            return $date;
        }

    }

    public function actionLoadCitrus()
    {
        $client = new Client();

        $t_log = [];
        for ($i = 0;$i<100;$i++)
        {
            $log = '['.$i.'] ';
            $t = time();
            $req = $client->request('GET','http://new-desktop.citrus.ua/api/main-page/rubrics/gift-ideas',
                ['auth'=>['admin','tzBYSW6alR']]);
            $log .= time()-$t.' ';
            $log .= $req->getStatusCode().' ';
            $t_log[$i] = $log;
        }

        $this->formatJson();
        return $t_log;

    }


}