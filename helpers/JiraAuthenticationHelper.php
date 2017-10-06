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

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://zapleo.atlassian.net/rest/api/2/']);
    }

    public function getUser($email, $password)
    {
        try {
            // Set various headers on a request
            $response = $this->client->request('GET', 'myself', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($email . ':' . $password),
                ]
            ]);

            if ($response->getStatusCode() != 200)
                return false;

            return $response->getBody() ? json_decode($response->getBody()) : false;
        } catch (RequestException $e) {
            return false;
        }
    }
}