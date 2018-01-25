<?php

namespace app\controllers;

use app\models\SignUpForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
//                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout','index','error','about'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions'=>['login','sign-up','error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $cookies = Yii::$app->request->cookies;

        if (!$cookies->has('key'))
            return $this->actionLogout();

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        if ($model->user_jira) {
            $signup_model = new SignUpForm();
            $signup_model->email = $model->email;
            $signup_model->password = $model->password;
            $signup_model->getUserFromJira();

            return $this->render('signup', ['model' => $signup_model]);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionSignUp()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignUpForm();

        if ($model->load(Yii::$app->request->post()) && $model->signup())
            return $this->goBack();

        if (!$model->user_jira)
            $this->redirect('/site/login');

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name' => 'key',
            'value' => '',
        ]));
        $cookies->add(new \yii\web\Cookie([
            'name' => 'token',
            'value' => '',
        ]));

        Yii::$app->user->logout();

        return $this->goHome();
    }
}
