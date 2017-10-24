<?php

namespace App\Wrappers\QuindWrapper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

class HTTPWrapper
{

    protected $password;
    protected $username;
    protected $client;

    public function __construct($username = null, $password = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->client = new Client(['verify' => false, 'timeout' => 10]);
    }


    public function get($url)
    {
        //Log::info($url);

        try
        {
            $result = $this->client->get($url, $this->setAuth())->getBody()->getContents();

            return json_decode($result);

        } catch (RequestException $e)
        {
            //Report to Quind
        } catch (ServerException $e)
        {
            //Report to Quind
        }
    }

    public function post($url, $data)
    {

        try
        {
            $this->client->post($url, ['json' => $data]);

        } catch (RequestException $e)
        {
            //Report to Quind
        } catch (ServerException $e)
        {
            //Report to Quind
        }
    }

    private function setAuth()
    {
        $result = [];
        if (isset($this->username))
        {
            $result = ['auth' => [$this->username, $this->password]];
        }

        return $result;
    }
}