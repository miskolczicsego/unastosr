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
//        $this->username = $data['sr-username'];
//        $this->apiKey = $data['sr-password'];
//        $this->url = $data['sr-api-url'];

//        //ÉLES SAJÁT
//        $this->username = 'test';
//        $this->apiKey = '2dcd07ef6f3515a5f3a00daba7967fb6';
//        $this->url = 'http://miskolczicsego.api.shoprenter.hu';

//        //Kiscipo SR
//        $this->username = 'kiscipo';
//        $this->apiKey = 'da79558ab84e46a7c6c84da5177ea501';
//        $this->url = 'http://kiscip.api.shoprenter.hu';

        //DEMO BENTI
        $this->username = 'test';
        $this->apiKey = '2dcd07ef6f3515a5f3a00daba7967fb6';
        $this->url = 'http://demo.api.aurora.miskolczicsego';

        //DEMO OTTHONI
//        $this->username = 'demo1234';
//        $this->apiKey = '585ce48b9192f37df1cb746f5b562b0a';
//        $this->url = 'http://demo.api.aurora.miskolczicsego';
    }
    /**
     * @return Config
     */
    public function getConfig()
    {
        return new Config($this->apiKey, $this->username, $this->url);
    }
}