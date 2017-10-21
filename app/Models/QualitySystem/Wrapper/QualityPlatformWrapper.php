<?php

namespace App\Models\QualitySystem\Wrapper;



abstract class QualityPlatformWrapper {

    protected $username;
    protected $password;
    protected $serverAPI;

    public function __construct($username, $password, $serverAPI)
    {
        $this->username = $username;
        $this->password = $password;
        $this->serverAPI = $serverAPI;
    }

    public abstract function getExternalMetricTypeOne($projectId, $externalMetrics);

    public abstract function getOpenIssues($projectId);
}