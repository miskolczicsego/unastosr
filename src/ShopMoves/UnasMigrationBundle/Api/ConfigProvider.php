<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 13:43
 */

namespace ShopMoves\UnasMigrationBundle\Api;

use ShopMoves\UnasMigrationBundle\Config\Config;

class ConfigProvider
{

    /**
     * @var string
     */
    protected $apiKey;

    protected $username;

    protected $url;


    public function setConfig()
    {
        $this->username = 'whiskynet_api';
        $this->apiKey = '77ae7bc2d855db24adf9d8e9c316ed98';
        $this->url = 'http://miskolczicsego.api.shoprenter.hu';
    }
    /**
     * @return Config
     */
    public function getConfig()
    {
        return new Config($this->apiKey, $this->username, $this->url);
    }
}