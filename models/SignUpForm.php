<?php

namespace app\models;

use app\components\Auth;
use app\helpers\JiraApiHelper;
use app\helpers\Security;
use Yii;
use yii\base\Model;
use app\helpers\JiraHelper;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 * @property array $user_jira
 *
 */
class SignUpForm extends Model
{
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $phone;
    public $skype;
    public $user_photo;
    public $user_jira = false;
    public $user_permissions = false;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password', 'phone', 'skype'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'User exist.'],
            // password is validated by validateAuthentication()
            ['password', 'validateAuthentication'],
            [['phone', 'skype'], 'string', 'max' => 30],
        ];
    }

    /**
     * Validates the Jira authentication.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateAuthentication($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->getUserFromJira()) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->email = $this->email;
            $user->team = 'ZapleoSoft';
            $user->phone = $this->phone;
            $user->skype = $this->skype;
            $user->photo = $this->user_photo;
            $user->role = User::ROLE_USER;

            if ($this->user_permissions && $this->user_permissions['permissions']['PROJECT_ADMIN']['havePermission'])
                $user->role = User::ROLE_ADMIN;

            if ($user->save()) {
                Security::encrypt($this->password, $this->user->id);

                return Yii::$app->user->login($this->getUser(), 3600 * 24 * 30);
            }
        }
        return false;
    }

    /**
     * Get user by [[email]]
     *
     * @return User|false
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }
        return $this->_user;
    }

    /**
     * @return array|bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     */
    public function getUserFromJira()
    {
        if ($this->user_jira === false) {
            $token = (new Auth([
                'username' => $this->email,
                'password' => $this->password
            ]))->getToken();

            if (empty($token))
                $this->user_jira = false;

            $jiraHelper = new JiraApiHelper($token);
            $this->user_jira = $jiraHelper->getUser();
            $this->user_permissions = $jiraHelper->getPermissions();

            if ($this->user_jira) {
                $this->getPhotoFromJira();
                $this->getUserNameFromJira();
            }
        }
        return $this->user_jira;
    }

    private function getPhotoFromJira()
    {
        if (!$this->user_photo)
            $this->user_photo = str_replace('s=48', '', $this->user_jira['avatarUrls']['48x48']);

        return true;
    }

    private function getUserNameFromJira()
    {
        if (!$this->first_name && !$this->last_name) {
            $user_name = ($this->user_jira['displayName'] ? $this->user_jira['displayName'] : $this->user_jira['name']);
            $this->first_name = $user_name;
            $this->last_name = '';

            if (strpos($user_name, ' '))
                list($this->first_name, $this->last_name) = explode(' ', $user_name);
        }

        return true;
    }
}
