<?php
/**
 * Created by PhpStorm.
 * User: zapleo
 * Date: 10.10.17
 * Time: 10:34
 */

namespace app\controllers;


use app\controllers\base\BaseController;

class SystemController extends BaseController
{

    public function actionIndex()
    {
        echo 'System Controller';
    }
}