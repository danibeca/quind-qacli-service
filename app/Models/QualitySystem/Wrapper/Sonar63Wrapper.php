<?php

namespace App\Models\QualitySystem\Wrapper;

use Illuminate\Support\Facades\Log;

class Sonar63Wrapper extends SonarWrapperV2
{

    public function getMetricsTypeOneUrl($projectId, $stringMetrics)
    {
        $result['base'] = $this->serverAPI;
        $result['resource'] = '/api/measures/component?componentKey=' . $projectId . '&metricKeys=' . $stringMetrics;

        return $result;
    }

    public function readMetricsTypeOneResponse($response)
    {
        return $response->component->measures;
    }

    public function transformMetricTypeOne($metric)
    {
        return [
            'code' => $metric->metric,
            'value' => $metric->value
        ];
    }

    public function transformIssue($issue)
    {
        return [
            'rule'         => $issue->rule,
            'severity'     => $issue->severity,
            'status'       => $issue->status,
            'message'      => $issue->message,
            'effort'       => isset($issue->effort) ? $issue->effort : 0,
            'debt'         => isset($issue->debt) ? $issue->debt : 0,
            'tags'         => $issue->tags,
            'type'         => $issue->type,
            'creationDate' => $issue->creationDate,
            'updateDate'   => $issue->updateDate
        ];
    }
}