<?php


namespace App\Models\Component;


use App\Wrappers\QuindWrapper\HTTPWrapper;

class Component
{

    public function __construct()
    {

    }

    public function getLeaves($key, $rootId, $serverURL)
    {
        $serviceURL = '/components/' . $rootId . '/leaves';
        $wrapper = new HTTPWrapper();

        return collect($wrapper->get($serverURL . $serviceURL));
    }
}