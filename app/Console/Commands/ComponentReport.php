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


    protected $signature = 'component:report {--componentId=} {--key=}';
    protected $description = 'Report information about components';
    protected $accounts;

    public function handle()
    {
        $qalogServer = 'http://qalog.quind.io/api/v1';

        $componentId = is_null($this->option('componentId')) ? 1 : $this->option('componentId');
        $key = is_null($this->option('key')) ? 'testing' : $this->option('key');

        $component = new Component();
        $components = $component->getLeaves($key, $componentId, $qalogServer);

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
            $extenalMetric->save($key, $component, $metricsValues, $qalogServer);

            $issuesValues = $issue->getFromServer($component);
            $issue->save($key, $component, $issuesValues, $qalogServer);

        }
    }
}
