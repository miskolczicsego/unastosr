<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 13:42
 */

namespace ShopMoves\UnasMigrationBundle\Api;


class ApiConfig
{
    protected $apiKey;
    /**
     * Api config constructor.
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }
    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}