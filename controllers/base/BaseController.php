<?php

/**
 * Created by PhpStorm.
 * User: zapleo
 * Date: 10.10.17
 * Time: 10:37
 */

namespace app\controllers\base;

use yii\filters\AccessControl;
use yii\web\Controller;

class BaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }
}