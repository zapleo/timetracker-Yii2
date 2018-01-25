<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: ProfileWidget.php
 * Date: 10.10.17
 * Time: 11:34
 */

namespace app\widgets;

use app\models\ManualTime;
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

        if (Yii::$app->user->identity->isAdmin())
            $manual_time = ManualTime::find()->where(['status' => ManualTime::STATUS_PENDING])->count();

        return $this->render('profile', [
            'user' => $user,
            'manual_time' => (!empty($manual_time) && $manual_time > 0 ? $manual_time : false)
        ]);
    }
}