<?php

namespace app\controllers;

use app\components\AccessRule;
use app\components\Auth;
use app\controllers\base\BaseController;
use app\helpers\JiraApiHelper;
use app\helpers\WorkLog as WorkLogHelper;
use app\models\ManualTime;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class ManualTimeController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'get-projects' => ['post'],
                    'get-issues' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'create', 'update', 'delete', 'accept', 'decline'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        // Allow users and admins to create, update and delete
                        'roles' => [
                            User::ROLE_USER,
                            User::ROLE_ADMIN
                        ],
                    ],
                    [
                        'actions' => ['accept', 'decline'],
                        'allow' => true,
                        // Allow admins to accept and decline
                        'roles' => [
                            User::ROLE_ADMIN
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * @param bool $status
     *
     * @param bool $user
     *
     * @return mixed
     */
    public function countItems($status = false, $user = false)
    {
        $query = ManualTime::find();

        if (!Yii::$app->user->identity->isAdmin()) {
            $query = $query->where(['user_id' => Yii::$app->user->id]);
        } elseif ($user != false) {
            $query = $query->where(['user_id' => $user]);
        }

        if ($status !== false)
            $query->andWhere(['status' => $status]);

        return $query->count();
    }

    /**
     * @param string $status
     *
     * @param bool   $user
     *
     * @return string
     */
    public function actionIndex($status = 'all', $user = false)
    {
        $query = ManualTime::find();

        if (!Yii::$app->user->identity->isAdmin()) {
            $query = $query->where(['user_id' => Yii::$app->user->id]);
        } elseif ($user != false) {
            $query = $query->where(['user_id' => $user]);
        }

        $model_status = [
            'pending' => ManualTime::STATUS_PENDING,
            'rejected' => ManualTime::STATUS_REJECTED,
            'added' => ManualTime::STATUS_ADDED
        ];

        if (!empty($status) && $status != 'all')
            $query->andWhere(['status' => $model_status[$status]]);

        $countQuery = clone $query;
        $pages = new Pagination(['pageSize' => 10, 'totalCount' => $countQuery->count()]);
        $models = $query->orderBy('updated_at DESC')
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $counters = [
            'all' => $this->countItems(false, $user),
            'pending' => $this->countItems(ManualTime::STATUS_PENDING, $user),
            'rejected' => $this->countItems(ManualTime::STATUS_REJECTED, $user),
            'added' => $this->countItems(ManualTime::STATUS_ADDED, $user)
        ];

        $users = User::find()->select(['id', 'email', 'hide'])->where(['hide' => User::STATUS_ACTIVE]);

        if (!Yii::$app->user->identity->isAdmin())
            $users = $users->andWhere(['id' => Yii::$app->user->id]);

        $users = $users->orderBy('email')->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'count' => $counters,
            'users' => $users
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ManualTime();
        $users = User::find()->select(['id', 'email', 'hide'])->where(['hide' => User::STATUS_ACTIVE])->orderBy('email')->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'users' => $users
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $users = User::find()->select(['id', 'email', 'hide'])->where(['hide' => User::STATUS_ACTIVE])->orderBy('email')->all();

        if (!Yii::$app->user->identity->isAdmin() && Yii::$app->user->id != $model->created_by)
            throw new BadRequestHttpException('Access denied!');

        if ($model->status === ManualTime::STATUS_ADDED)
            throw new BadRequestHttpException('You can not edit this item!');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'users' => $users
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->identity->isAdmin() && Yii::$app->user->id != $model->created_by)
            throw new BadRequestHttpException('Access denied!');

        $status = $model->status;

        if ($model->delete() && $status === ManualTime::STATUS_ADDED)
            (new WorkLogHelper())->deleteManualTime($id);

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAccept($id)
    {
        $model = $this->findModel($id);

        if ($model->status === ManualTime::STATUS_ADDED)
            throw new BadRequestHttpException('Manual time added!');

        $model->status = ManualTime::STATUS_ADDED;

        if ($model->save())
            (new WorkLogHelper())->addManualTime($model);

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDecline($id)
    {
        $model = $this->findModel($id);

        if ($model->status === ManualTime::STATUS_REJECTED)
            throw new BadRequestHttpException('Manual time rejected!');

        $status = $model->status;

        $model->status = ManualTime::STATUS_REJECTED;

        if ($model->save() && $status === ManualTime::STATUS_ADDED)
            (new WorkLogHelper())->deleteManualTime($model->id);

        return $this->redirect(['index']);
    }

    /**
     * @return array
     * @throws \understeam\jira\Exception
     */
    public function actionGetProjects()
    {
        $this->formatJson();

        $user = Yii::$app->request->post('user',false);

        if (!Yii::$app->user->identity->isAdmin())
            $user = Yii::$app->user->identity->email;

        $token = (new Auth())->getToken();
        $projects = (new JiraApiHelper($token))->getProjectsFromIssues($user);

        return $projects;
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     * @throws \understeam\jira\Exception
     */
    public function actionGetIssues()
    {
        $this->formatJson();

        $user = Yii::$app->request->post('user',false);
        $project = Yii::$app->request->post('project',false);

        if (!Yii::$app->user->identity->isAdmin())
            $user = Yii::$app->user->identity->email;

        $token = (new Auth())->getToken();
        $issues = (new JiraApiHelper($token))->getOpenIssues($user, $project);

        return $issues;
    }

    /**
     * Finds the SourceLangMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ManualTime the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ManualTime::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
