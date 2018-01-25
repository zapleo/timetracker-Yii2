<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: JiraClient.php
 * Date: 23.01.18
 * Time: 10:18
 */

namespace app\components;

use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\helpers\Json;

class Client extends \understeam\jira\Client
{
    public $token;

    /**
     * @return string
     */
    public function getApiEndpointUrl()
    {
        $url = !empty($this->jiraUrl) ? $this->jiraUrl : \Yii::$app->params['jira_url'];
        return rtrim($url, '/') . '/rest/api/2/';
    }

    /**
     * @param       $method
     * @param       $path
     * @param array $body
     *
     * @return bool|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     */
    public function request($method, $path, $body = [])
    {
        if (empty($this->token))
            return false;

        $url = $this->getUrlOfPath($path);

        if (is_array($body) && !empty($body)) {
            $body = Json::encode($body);
        } else {
            $body = '';
        }

        $cacheKey = md5($method . $url . $body);
        $result = Yii::$app->cache->get($cacheKey);

        if ($result !== false) {
            return $result;
        }

        try {
            //$authString = base64_encode($this->username . ':' . $this->password);
            $result = $this->httpClient->request($method, $url, $body, [
                'headers' => [
                    //'Authorization' => 'Basic ' . $authString,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Cookie' => $this->token
                ]
            ]);


            if (is_string($result)) {
                $result = Json::decode($result);
            }

            Yii::trace($url . "\n" . $body, __CLASS__);
        } catch (RequestException $e) {
            $result = $e->getResponse()->getBody()->__toString();
            $loginReason = $e->getResponse()->getHeader('X-Seraph-LoginReason');
            $contentType = $e->getResponse()->getHeader('Content-Type');

            if (is_array($loginReason))
                $loginReason = array_shift($loginReason);

            if ($loginReason == 'AUTHENTICATED_FAILED')
                return false;

            if (is_array($contentType))
                $contentType = array_shift($contentType);

            if (strpos($contentType, 'application/json') !== false) {
                $result = Json::decode($result);
            }

            Yii::error($result, __CLASS__);
        }

        Yii::$app->cache->set($cacheKey, $result, $this->cacheDuration);

        return $result;
    }
}