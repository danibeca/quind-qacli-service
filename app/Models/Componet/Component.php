<?php


namespace App\Models\Component;


class Component
{

    public function getComponents($componentId, $key, $url)
    {
        $client = new Client();
        try
        {
            $client->get();

        } catch (RequestException $e)
        {
            return $this->respond(false);
        }

        return $this->respond(true);

    }


}