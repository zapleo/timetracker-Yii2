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

        $timeStart = \Yii::$app->request->get('timeStart');
        $timeEnd = \Yii::$app->request->get('timeEnd');

        $timeStart = is_null($timeStart) ? false :$timeStart;
        $timeEnd = is_null($timeEnd) ? false :$timeEnd;

        if (!$end) {

            $timeStart = \DateTime::createFromFormat('d/m/Y', $timeStart)->format('Y-m-d 00:00:00');

            return $timeStart;

        } else {

            if (!$timeEnd) {
                $timeEnd = \DateTime::createFromFormat('d/m/Y', $timeStart)->format('Y-m-d 23:59:00');
            } else {
                $timeEnd = \DateTime::createFromFormat('d/m/Y', $timeEnd)->format('Y-m-d 23:59:00');
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
                $timeStart = $this->getDate();
                $timeEnd = $this->getDate(1);
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
     * @return array|int
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
                $timeStart = $this->getDate();
                $timeEnd = $this->getDate(1);
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

        return 0;
    }


}