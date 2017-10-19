<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: JiraAuthenticationHelper.php
 * Date: 06.10.17
 * Time: 11:44
 */

namespace app\helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class JiraAuthenticationHelper
{
    protected $client;
    protected $authorization = '';

    public function __construct($email, $password)
    {
        $this->client = new Client(['base_uri' => 'https://zapleo.atlassian.net/rest/api/2/']);
        $this->authorization = 'Basic ' . base64_encode($email . ':' . $password);
    }

    public function getUser()
    {
        try {
            // Set various headers on a request
            $response = $this->client->request('GET', 'myself', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $this->authorization,
                ]
            ]);

            if ($response->getStatusCode() != 200)
                return false;

            return $response->getBody() ? json_decode($response->getBody(), true) : false;
        } catch (RequestException $e) {
            return false;
        }
    }

    public function getPermissions()
    {
        try {
            // Set various headers on a request
            $response = $this->client->request('GET', 'mypermissions', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $this->authorization,
                ]
            ]);

            if ($response->getStatusCode() != 200)
                return false;

            return $response->getBody() ? json_decode($response->getBody(), true) : false;
        } catch (RequestException $e) {
            return false;
        }
    }
}