<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: Security.php
 * Date: 23.01.18
 * Time: 11:16
 */

namespace app\helpers;

use app\models\User;

class Security
{
    private static $key = false;

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    private static function addKey()
    {
        $key = \Yii::$app->security->generateRandomString();
        $cookies = \Yii::$app->response->cookies;

        $cookies->add(new \yii\web\Cookie([
            'name' => 'key',
            'value' => $key,
            'path' => '/'
        ]));

        return $key;
    }

    /**
     * @return mixed
     */
    private static function getKey()
    {
        if (empty(self::$key)) {
            $cookies = \Yii::$app->request->cookies;
            self::$key = $cookies->getValue('key', false);
        }

        if (self::$key === false) {
            \Yii::$app->user->logout();
            return \Yii::$app->response->redirect(['/login']);
        }

        return self::$key;
    }

    /**
     * @param      $data
     *
     * @param null $user_id
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function encrypt($data, $user_id = null)
    {
        $encryptedData = \Yii::$app->getSecurity()->encryptByKey($data, self::addKey());
        $encryptedData = base64_encode($encryptedData);

        if (empty($user_id))
            $user_id = \Yii::$app->user->id;

        $user = User::findOne($user_id);
        $user->token_hash = $encryptedData;

        return $user->save() ? true : false;
    }

    /**
     * @param null $user_id
     *
     * @return bool|string
     */
    public static function decrypt($user_id = null)
    {
        if (empty($user_id))
            $user_id = \Yii::$app->user->id;

        $user = User::findOne($user_id);

        $data = \Yii::$app->getSecurity()->decryptByKey(base64_decode($user->token_hash), self::getKey());

        return !empty($data) ? $data : false;
    }
}