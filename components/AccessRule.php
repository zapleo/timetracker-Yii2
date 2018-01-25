<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: AccessRule.php
 * Date: 22.01.18
 * Time: 13:48
 */

namespace app\components;

use app\models\User;

class AccessRule extends \yii\filters\AccessRule {

    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role == '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role == User::ROLE_USER) {
                if (!$user->getIsGuest()) {
                    return true;
                }
                // Check if the user is logged in, and the roles match
            } elseif (!$user->getIsGuest() && $role == $user->identity->role) {
                return true;
            }
        }

        return false;
    }
}
