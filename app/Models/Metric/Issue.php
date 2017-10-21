<?php


namespace App\Models\Metric;


use App\Wrappers\QuindWrapper\HTTPWrapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class Issue
{

    public function __construct()
    {

    }


    public function getFromServer($component)
    {
        $wrapperServerMetrics = new $component->wrapper_class($component->username, $component->password, $component->url);
        $appCode = $component->app_code;

        return $wrapperServerMetrics->getOpenIssues($appCode);
    }

    public function save($key, $component, $values, $serverURL)
    {

        $serviceURL = '/components/' . $component->id . '/issue-values';
        $wrapper = new HTTPWrapper();
        $wrapper->post($serverURL . $serviceURL, $values);

    }

}