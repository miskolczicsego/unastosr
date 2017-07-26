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

//        //ÉLES
//        $this->username = 'test';
//        $this->apiKey = '2dcd07ef6f3515a5f3a00daba7967fb6';
//        $this->url = 'http://miskolczicsego.api.shoprenter.hu';

//        //Kiscipo
//        $this->username = 'kiscipo';
//        $this->apiKey = 'da79558ab84e46a7c6c84da5177ea501';
//        $this->url = 'http://kiscip.api.shoprenter.hu';

        //DEMO
        $this->username = 'test';
        $this->apiKey = '2dcd07ef6f3515a5f3a00daba7967fb6';
        $this->url = 'http://demo.api.aurora.miskolczicsego';
    }
    /**
     * @return Config
     */
    public function getConfig()
    {
        return new Config($this->apiKey, $this->username, $this->url);
    }
}