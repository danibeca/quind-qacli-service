<?php

namespace App\Console\Commands;


use App\Models\Component\Component;
use App\Models\Metric\ExtenalMetric;
use App\Models\Metric\Issue;
use App\Wrappers\QuindWrapper\HTTPWrapper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ComponentReport extends Command
{


    protected $signature = 'component:report';
    protected $description = 'Report information about components';
    protected $accounts;

    public function handle()
    {
        $qalogServer = env('QUIND_ENDPOINT');
        $key = '';

        $serviceURL = $qalogServer . '/api-clients/' . env('CLIENT_CODE') . '/roots';
        $wrapper = new HTTPWrapper();
        $rootIds = $wrapper->get($serviceURL);

        foreach ($rootIds as $rootId)
        {
            $component = new Component();
            $components = $component->getLeaves($key, $rootId, $qalogServer);
            $qaSystemIds = $components->pluck('quality_system_id')->unique();

            $metricsArray = array();
            foreach ($qaSystemIds as $qaSystemId)
            {
                $metric = new ExtenalMetric();
                $metricsArray[$qaSystemId] = $metric->getExtenalMetrics($key, $qaSystemId, $qalogServer);
            }

            $extenalMetric = New ExtenalMetric();
            $issue = New Issue();
            foreach ($components as $component)
            {
                $extenalMetrics = $metricsArray[$component->quality_system_id];
                $metricsValues = $extenalMetric->getFromServer($component, $extenalMetrics);

                $result = [];
                $collectionExternalMetrics = collect($extenalMetrics);
                foreach ($metricsValues as $metricsValue)
                {
                    $metricsValue['metric_id'] = $collectionExternalMetrics->where('code', $metricsValue['code'])->first()->metric_id;
                    array_push($result, $metricsValue);
                }

                $extenalMetric->save($key, $component, $result, $qalogServer);

                $issuesValues = $issue->getFromServer($component);
                $issue->save($key, $component, $issuesValues, $qalogServer);
            }
        }
    }

}
