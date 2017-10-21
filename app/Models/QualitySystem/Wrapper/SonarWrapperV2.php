<?php

namespace App\Models\QualitySystem\Wrapper;

use App\Wrappers\QuindWrapper\HTTPWrapper;
use Illuminate\Support\Facades\Log;

abstract class SonarWrapperV2 extends QualityPlatformWrapper
{

    protected $client;

    public function __construct($username, $password, $serverAPI, $client = null, $response = null)
    {
        parent::__construct($username, $password, $serverAPI);
        if ($client !== null)
        {
            $this->client = $client;

        } else
        {
            $this->client = new HTTPWrapper($username, $password);
        }
    }

    public abstract function transformIssue($issue);

    public abstract function getMetricsTypeOneUrl($projectId, $stringMetrics);

    public abstract function readMetricsTypeOneResponse($response);

    public abstract function transformMetricTypeOne($metric);

    public function getExternalMetricTypeOne($projectId, $stringMetrics)
    {
        return $this->transformCollection(
            $this->readMetricsTypeOneResponse(
                $this->request(
                    $this->getMetricsTypeOneUrl($projectId, $stringMetrics))));

    }

    public function getOpenIssueUrl($projectId, $page = 1)
    {
        $result['base'] = $this->serverAPI;
        $result['resource'] = '/issues/search?componentKeys=' . $projectId . '&statuses=OPEN,REOPENED&p=' . $page;

        return $result;
    }

    public function readNumberOfPages($response)
    {
        $paging = $response->paging;

        return ceil($paging->total / $paging->pageSize);
    }

    public function readOpenIssueResponse($response)
    {
        return $this->transformIssueCollection($response->issues);
    }

    public function getOpenIssues($projectId)
    {
        $pages = $this->readNumberOfPages($this->request(
            $this->getOpenIssueUrl($projectId)));
        $result = null;

        $pages = ($pages > 0) ? $pages : 1;
        for ($i = 1; $i <= $pages; $i++)
        {
            $result = (object)array_merge((array)$result,(array)$this->readOpenIssueResponse(
                $this->request(
                    $this->getOpenIssueUrl($projectId, $i))));
        }


        return $result;
    }

    public function request($url)
    {
        return $this->client->get($url['base'] . $url['resource']);
    }

    public function transformCollection(array $metrics)
    {
        return array_map([$this, 'transformMetricTypeOne'], $metrics);
    }

    public function transformIssueCollection(array $items)
    {
        return array_map([$this, 'transformIssue'], $items);
    }

}
