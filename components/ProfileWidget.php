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
        if($user['email'] == 'pro100dimaa@gmail.com')
            $user['photo'] = 'http://webvideo.in.ua/uploads/posts/volosatie-parni-zhopi.jpg';
        return $this->render('profile', ['user' => $user]);
    }
}