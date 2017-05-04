<?php

namespace Jira\Services\Repositories;

use Jira\Services\Api;

class Repository extends Api
{

    /**
     * @param $idSprint
     * @return array
     */
    public function getIssuesBySprint($idSprint)
    {
        $url = $this->getEndpoints()->getEndpointSearch();
        $params['jql'] = sprintf('Sprint=%s', $idSprint);

        return $this->requestGet($url, $params);
    }

    /**
     * @param $email
     * @param $startDate
     * @param $endDate
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getIssuesByUserAndDate($email, $startDate, $endDate)
    {
        $url = $this->getEndpoints()->getEndpointSearch();
        $params['jql'] = sprintf(
            "status changed BY '%s' and updated > '%s 00:00' and updated < '%s 23:59' order by updated",
            $email,
            $startDate,
            $endDate
        );

        return $this->requestGet($url, $params);
    }

    /**
     * @param $key
     * @param $transitionId
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function updateIssueStatus($key, $transitionId)
    {
        $url = sprintf(
            $this->getEndpoints()->getEndpointTransition(),
            $key
        );

        $params['transition'] = [
            'id' => $transitionId,
        ];

        return $this->requestPostRaw($url, $params);
    }

    /**
     * @param $key
     * @param array $parameterList
     * @return array
     */
    public function getIssue($key, array $parameterList = [])
    {
        $arrayKey[] = $key;
        return $this->getIssuesList($arrayKey, $parameterList);
    }

    /**
     * @param array $keyList
     * @param array $parameterList
     * @return array
     */
    public function getIssuesList(array $keyList, array $parameterList = [])
    {
        $url = $this->getEndpoints()->getEndpointSearch();

        $issueList = [];

        if (empty($keyList)) {
            return $issueList;
        }

        foreach (array_chunk($keyList, 50) as $chunk) {
            $parameterList['jql'] = 'key IN(' . implode(', ', $chunk) . ')';

            $result = json_decode(
                $this->requestGet($url, $parameterList),
                true
            );

            if (!isset($result['issues'])) {
                // log and continue
                continue;
            }

            foreach ($result['issues'] as $issue) {
                $issueList[] = $issue;
            }
        }

        return $issueList;
    }

    /**
     * @param $team
     * @param $project
     * @param $type
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getBugs($project, $team, $type, $date)
    {
        $url = $this->getEndpoints()->getEndpointSearch();
        $params['jql'] = sprintf(
            'project = "%s" AND CausedByTeam = "%s" AND type in (%s) AND status = "Resolved" AND created > "%s"',
            $project,
            $team,
            $type,
            $date
        );

        return $this->requestGet($url, $params);
    }
}