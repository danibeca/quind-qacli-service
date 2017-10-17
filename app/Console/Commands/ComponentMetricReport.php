<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ComponentMetricReport extends Command
{


    protected $signature = 'component:report {--componentId=} {--key=}';
    protected $description = 'Report information about components';
    protected $accounts;

    public function handle()
    {
        $componentId = $this->option('componentId');

        $key = is_null($this->option('key')) ? 'testing': $this->option('key');

        Log::info('Finally'.$key.$componentId);

    }

}
