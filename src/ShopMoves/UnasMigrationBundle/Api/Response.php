<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 13:42
 */

namespace ShopMoves\UnasMigrationBundle\Api;


class Response
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * Response constructor.
     * @param $data
     * @param $info
     * @param $statusCode
     */
    public function __construct($data, $info, $statusCode)
    {
        $this->data = $data;
        $this->info = $info;
        $this->statusCode = $statusCode;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->statusCode;
    }
}