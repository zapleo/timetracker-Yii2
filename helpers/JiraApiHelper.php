<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: JiraApiHelper.php
 * Date: 23.01.18
 * Time: 13:41
 */

namespace app\helpers;

use app\components\Client;
use app\models\User;
use understeam\jira\Exception;

class JiraApiHelper
{
    public $client;

    private $issues = [];

    public function __construct($token)
    {
        $this->client = new Client([
            'token' => $token
        ]);
    }

    /**
     * @param bool $email
     *
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     */
    public function getUser($email = false)
    {
        if (empty($email)) {
            $user = $this->client->get('myself');
        } else {
            $user = $this->client->get('user/search', ['username' => $email]);
        }

        return (isset($user[0]) ? $user[0] : $user);
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     */
    public function getPermissions()
    {
        return $this->client->get('mypermissions');
    }

    /**
     * @param      $jql
     *
     * @param bool $all
     * @param int  $max
     * @param int  $start
     *
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     * @throws \understeam\jira\Exception
     */
    public function searchIssues($jql, $all = true, $max = 50, $start = 0)
    {
        $result = $this->client->post('search', [
            'jql' => $jql,
            'maxResults' => $max,
            'startAt' => $start,
            'fields' => [
                'summary',
                'status',
                'assignee',
                'project'
            ]
        ]);

        if (isset($result['errorMessages'])) {
            throw new Exception('Jira search error: ' . $result['errorMessages'][0]);
        }

        $this->issues = array_merge($this->issues, $result['issues']);
        $count_issues = count($this->issues);

        if ($all && $result['total'] > $count_issues)
            $this->searchIssues($jql, true, $max, $count_issues);

        return $this->issues;
    }

    /**
     * @param bool $user
     * @param bool $project
     *
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     * @throws \understeam\jira\Exception
     */
    public function getOpenIssues($user = false, $project = false)
    {
        $jql = '';

        if ($user) {
            $jql .= 'assignee = \''.$user.'\' AND ';
        }

        if (!empty($project)) {
            $jql .= 'project = '.$project.' AND ';
        }

        $jql .= 'statuscategory != done';

        return $this->searchIssues($jql);
    }

    /**
     * @param bool $user
     *
     * @return array
     * @throws \understeam\jira\Exception
     */
    public function getProjectsFromIssues($user = false)
    {
        $issues = $this->getOpenIssues($user);

        $projects = [];

        foreach ($issues as $issue) {
            $project = $issue['fields']['project'];
            $projects[$project['key']] = $project['name'];
        }

        return $projects;
    }

    /**
     * @param $issue
     * @param $started
     *
     * @param $time_spent
     * @param $comment
     *
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
     * @throws \understeam\jira\Exception
     */
    public function addWorkLog($issue, $started, $time_spent, $comment)
    {
        $data = [
            'comment' => (empty($comment) ? 'Manual time' : $comment),
            'started' => date('Y-m-d\TH:i:s.vO', $started),
            'timeSpent' => ($time_spent / 60).'m'
        ];

        $work_log = $this->client->post('issue/'.$issue.'/worklog', $data);

        if (isset($worl_log['errorMessages'])) {
            throw new Exception('Jira search error: ' . $work_log['errorMessages'][0]);
        }

        return $work_log;
    }

    /**
     * @param $issue
     * @param $work_log_id
     */
    public function deleteWorkLog($issue, $work_log_id)
    {
        $this->client->delete('issue/'.$issue.'/worklog/'.$work_log_id);
    }
}