<?php

namespace Jira\Services;

use Jira\Services\Http\Authentication\Basic;
use Jira\Services\Http\Client;

class Api
{

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Authentication object
     * @var Basic
     */
    protected $auth;

    /**
     * @var Endpoints
     */
    protected $endpoints;

    /**
     * Api constructor.
     * @param array $configuration
     * @throws \Exception
     */
    public function __construct(array $configuration)
    {
        if (!isset($configuration['host'])) {
            throw new \Exception ('Host not found');
        }

        $this->httpClient = new Client($configuration['host']);
        $this->setData($configuration);
    }

    /**
     * @param array $configuration
     * @throws \Exception
     */
    protected function setData(array $configuration)
    {
        if (!isset($configuration['user'])) {
            throw new \Exception ('User not found');
        }

        if (!isset($configuration['password'])) {
            throw new \Exception ('Password not found');
        }

        $this->endpoints = $this->setEndpoints($configuration);
        $credentials = $this->createCredentials($configuration['user'], $configuration['password']);
        $this->setCredentials($credentials);
    }

    /**
     * @param $user
     * @param $password
     * @return Basic
     */
    protected function createCredentials($user, $password)
    {
        return new Basic($user, $password);
    }

    /**
     * @param array $configuration
     * @return Endpoints
     */
    protected function setEndpoints(array $configuration)
    {
        return new Endpoints($configuration);
    }

    /**
     * @return Endpoints
     */
    protected function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * @param Basic $basicAuthentication
     */
    public function setCredentials(Basic $basicAuthentication)
    {
        $this->auth = $basicAuthentication;
        $this->httpClient->addOptions([
            'auth' => [
                $basicAuthentication->getUsername(),
                $basicAuthentication->getPassword()
            ]
        ]);
    }

    /**
     * @return Basic
     */
    public function getCredentials()
    {
        return $this->auth;
    }

    /**
     * @access public
     * @return Client
     */
    public function getClient()
    {
        return $this->httpClient;
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestGet($endpoint, $params = array())
    {
        return $this->getClient()->get($endpoint, $params);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestPost($endpoint, $params = array())
    {
        return $this->getClient()->post($endpoint, $params);
    }

    /**
     * @param $endpoint
     * @param $raw
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function requestPostRaw($endpoint, $raw)
    {
        return $this->getClient()->postRaw($endpoint, $raw);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestPut($endpoint, $params = array())
    {
        return $this->getClient()->put($endpoint, $params);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function requestDelete($endpoint, $params = array())
    {
        return $this->getClient()->delete($endpoint, $params);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function doRequest($method, $endpoint, $params)
    {
        return $this->getClient()->request($endpoint, $params, $method);
    }

    /**
     * Convert JSON to array with error check
     *
     * @access protected
     * @param  string $body JSON data
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function decodeJSON($body)
    {
        $params = json_decode($body, true);

        if (!is_array($params) || (JSON_ERROR_NONE !== json_last_error())) {
            throw new \InvalidArgumentException('Invalid JSON data provided.');
        }

        return $params;
    }
}
