<?php

namespace Jira\Services;

class Endpoints
{
    /**
     * string method post
     */
    const ISSUE_STATUS_IN_REVIEW = '41';

    /**
     * @var string
     */
    protected $endpointMeblo = '/browse/MEBLO-';

    /**
     * @var string
     */
    protected $endpointSearch = '/rest/api/2/search';

    /**
     * @var string
     */
    protected $endpointTransition = '/rest/api/2/issue/%s/transitions';


    /**
     * Configuration constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->setData($configuration);
    }

    /**
     * @param array $configuration
     */
    protected function setData(array $configuration)
    {
        foreach ($configuration as $key => $value) {
            if (property_exists($this, $key) && strlen($value)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param $endpointMeblo
     */
    public function setEndpointMeblo($endpointMeblo)
    {
        $this->endpointMeblo = $endpointMeblo;
    }

    /**
     * @return string
     */
    public function getEndpointMeblo()
    {
        return $this->endpointMeblo;
    }

    /**
     * @param $endpointTransition
     */
    public function setEndpointTransition($endpointTransition)
    {
        $this->endpointTransition = $endpointTransition;
    }

    /**
     * @return string
     */
    public function getEndpointTransition()
    {
        return $this->endpointTransition;
    }

    /**
     * @param $endpointSearch
     */
    public function setEndpointSearch($endpointSearch)
    {
        $this->endpointSearch = $endpointSearch;
    }

    /**
     * @return string
     */
    public function getEndpointSearch()
    {
        return $this->endpointSearch;
    }
}

