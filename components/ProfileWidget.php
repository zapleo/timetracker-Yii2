<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: ProfileWidget.php
 * Date: 10.10.17
 * Time: 11:34
 */

namespace app\components;

use Yii;
use yii\base\Widget;

class ProfileWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $user = Yii::$app->user->identity->attributes;

        return $this->render('profile', ['user' => $user]);
    }
}