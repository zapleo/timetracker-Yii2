<?php

namespace app\models;

use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $last_name
 * @property string $first_name
 * @property string $email
 * @property string $team
 * @property string $photo
 * @property string $phone
 * @property string $skype
 * @property integer $rights
 * @property integer $hide
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 *
 * @property WorkLog[] $workLogs
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 0;
    const STATUS_HIDE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_name', 'first_name', 'email', 'team', 'skype'], 'required'],
            [['rights', 'hide'], 'integer'],
            [['last_name', 'first_name', 'team', 'phone', 'skype'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 40],
            [['photo', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'email' => 'Email',
            'team' => 'Team',
            'photo' => 'Photo',
            'phone' => 'Phone',
            'skype' => 'Skype',
            'rights' => 'Rights',
            'hide' => 'Hide',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkLogs()
    {
        return $this->hasMany(WorkLog::className(), ['user_id' => 'id']);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'hide' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @param null $type
     * @return IdentityInterface|null the identity object that matches the given token.
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Find user by eMail
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'hide' => self::STATUS_ACTIVE]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password_hash === $password;
    }

    public function isAdmin()
    {
        return $this->rights;
    }
}
