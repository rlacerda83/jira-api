<?php

namespace Jira\Services\Http;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * Api response codes.
     */
    const HTTP_RESPONSE_OK              = 200;
    const HTTP_RESPONSE_CREATED         = 201;
    const HTTP_RESPONSE_NO_CONTENT      = 204;
    const HTTP_RESPONSE_BAD_REQUEST     = 400;
    const HTTP_RESPONSE_UNAUTHORIZED    = 401;
    const HTTP_RESPONSE_FORBIDDEN       = 403;
    const HTTP_RESPONSE_NOT_FOUND       = 404;

    /**
     * @var string
     */
    protected $baseUri;

    const FORMAT = 'json';

    /**
     * @var array
     */
    protected $options = [
        'verify' => false,
    ];

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Client constructor.
     * @param $host
     */
    public function __construct($host)
    {
        $this->baseUri = $host;
        $this->client = new GuzzleClient();
    }

    /**
     * @param array $options
     */
    public function addOptions(array $options)
    {
        $this->options += array_merge($this->options, $options);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return bool|ResponseInterface
     */
    public function get($endpoint, $params = array())
    {
        if (is_array($params) && count($params) > 0) {
            $endpoint .= (strpos($endpoint, '?') === false ? '?' : '&').http_build_query($params, '', '&');
            $params = array();
        }

        return $this->request($endpoint, $params, 'GET');
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return bool|ResponseInterface
     */
    public function post($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, 'POST');
    }

    /**
     * @param $endpoint
     * @param $raw
     * @return bool|ResponseInterface
     */
    public function postRaw($endpoint, $raw)
    {
        return $this->request($endpoint, [], 'POST', $raw);
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return bool|ResponseInterface
     */
    public function put($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, 'PUT');
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return bool|ResponseInterface
     */
    public function delete($endpoint, $params = array())
    {
        return $this->request($endpoint, $params, 'DELETE');
    }

    /**
     * @param $endpoint
     * @param array $params
     * @param string $method
     * @param null $rawData
     * @return bool|ResponseInterface
     * @throws \Exception
     */
    public function request($endpoint, $params = array(), $method = 'GET', $rawData = null)
    {
        $url = $this->getApiBaseUrl().'/'.$endpoint;

        // change the response format
        if (strpos($url, 'format=') === false) {
            $url .= (strpos($url, '?') === false ? '?' : '&').'format='.$this->getResponseFormat();
        }

        // add a default content-type if none was set
        if (in_array(strtoupper($method), array('POST', 'PUT')) && empty($headers['Content-Type'])) {
            $this->addOptions([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]);

            if ($rawData !== null) {
                $this->addOptions([
                    'json' => $rawData
                ]);
            }
        }

        if (!empty($params)) {
            $this->addOptions([
                'form_params' => $params
            ]);
        }

        $response = $this->client->request($method, $url, $this->options);

        return $this->processResponse($response);
    }

    /**
     * @return GuzzleClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getResponseFormat()
    {
        return self::FORMAT;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->baseUri;
    }

    /**
     * @param ResponseInterface $response
     * @return bool|ResponseInterface|string
     * @throws \Exception
     */
    protected function processResponse(ResponseInterface $response)
    {
        switch ($response->getStatusCode()) {
            case self::HTTP_RESPONSE_OK:
            case self::HTTP_RESPONSE_CREATED:
                return $response->getBody()->getContents();
                break;

            case self::HTTP_RESPONSE_NO_CONTENT:
                return true;
                break;

            case self::HTTP_RESPONSE_BAD_REQUEST:
                return $response;

            case self::HTTP_RESPONSE_UNAUTHORIZED:
                throw new \Exception("Unauthorized: Authentication required");
                break;

            case self::HTTP_RESPONSE_FORBIDDEN:
                throw new \Exception("Not enough permissions.");
                break;

            case self::HTTP_RESPONSE_NOT_FOUND:
                return false;
                break;

            default:
                return $response;
                break;
        }
    }
}