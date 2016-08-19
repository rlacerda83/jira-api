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

        $params = json_encode([
            'transition' => [
                'id' => $transitionId,
            ],
        ]);
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
}