<?php


namespace App\Models\Metric;

use App\Wrappers\QuindWrapper\HTTPWrapper;

class ExtenalMetric
{

    public function __construct()
    {

    }

    public function getExtenalMetrics($key, $qaSystemId, $serverURL)
    {
        $serviceURL = '/metrics?qualitySystem=' . $qaSystemId;
        $wrapper = new HTTPWrapper();

        return collect($wrapper->get($serverURL . $serviceURL));
    }

    public function getFromServer($component, $externalMetrics)
    {
        $wrapperServerMetrics = new $component->wrapper_class($component->username, $component->password, $component->url);
        $appCode = $component->app_code;
        $externalMetricValues = $this->getExternalMetricTypeOne($appCode, collect($externalMetrics), $wrapperServerMetrics);

        return $externalMetricValues;
    }

    public function getExternalMetricTypeOne($appCode, $externalMetrics, $wrapperServerMetrics)
    {
        $result = null;
        $extMetricTypeOne = $externalMetrics->where('type', 1);
        if ($extMetricTypeOne->count() > 0)
        {
            $stringMetrics = $extMetricTypeOne->implode('code', ',');
            $result = $wrapperServerMetrics->getExternalMetricTypeOne($appCode, $stringMetrics);

        }

        return $result;
    }

    public function save($key, $component, $values, $serverURL)
    {

        $serviceURL = '/components/' . $component->id . '/metric-values';
        $wrapper = new HTTPWrapper();
        $wrapper->post($serverURL . $serviceURL, $values);

    }
}