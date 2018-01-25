<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: JiraClient.php
 * Date: 23.01.18
 * Time: 10:18
 */

namespace app\components;

use app\helpers\Security;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * @property mixed httpClient
 * @property mixed jiraUsername
 * @property mixed jiraPassword
 * @property mixed jiraUrl
 */
class Auth extends Component
{
    public $url;

    public $username;
    public $password;

    public $httpClientId = 'httpclient';

    /**
     * @return mixed
     */
    protected function getJiraUsername()
    {
        return !empty($this->username) ? $this->username : \Yii::$app->user->identity->email;
    }

    /**
     * @return bool|string
     */
    protected function getJiraPassword()
    {
        return !empty($this->password) ? $this->password : Security::decrypt();
    }

    /**
     * @return mixed
     */
    public function getJiraUrl()
    {
        return !empty($this->url) ? $this->url : \Yii::$app->params['jira_url'];
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return rtrim($this->jiraUrl, '/') . '/rest/auth/1/session/';
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function getHttpClient()
    {
        return Yii::$app->get($this->httpClientId);
    }

    protected function requestToken()
    {
        $body = Json::encode([
            'username' => $this->jiraUsername,
            'password' => $this->jiraPassword
        ]);

        try {
            $result = $this->httpClient->request('POST', $this->getAuthUrl(), $body, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            if (is_string($result)) {
                $result = Json::decode($result);
            }
        } catch (RequestException $e) {
            $result = $e->getResponse()->getBody()->__toString();
            $contentType = $e->getResponse()->getHeader('Content-Type');

            if (is_array($contentType))
                $contentType = array_shift($contentType);

            if (strpos($contentType, 'application/json') !== false) {
                $result = Json::decode($result);
            }

            Yii::error($result, __CLASS__);
        }

        if (empty($result['session']))
            return false;

        $token = $result['session']['name'].'='.$result['session']['value'];

        $cookies = \Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name' => 'token',
            'value' => $token,
            'path' => '/'
        ]));

        return $token;
    }

    public function getToken()
    {
        $cookies = \Yii::$app->request->cookies;
        $token = $cookies->getValue('token', false);

        if (empty($token) || (!empty($this->username) && !empty($this->password)))
            $token = $this->requestToken();

        return $token;
    }
}