<?php

namespace ShopMoves\UnasMigrationBundle\Config;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.31.
 * Time: 11:56
 */
class Config
{
    protected $apiKey;
    protected $username;
    protected $url;

    function __construct($apikey, $username, $url)
    {
        $this->apiKey = $apikey;
        $this->username = $username;
        $this->url = $url;
    }
    public function getApiKey()
    {
        return $this->apiKey;
    }
    public function getUserName()
    {
        return $this->username;
    }
    public function getUrl()
    {
        return $this->url;
    }
}