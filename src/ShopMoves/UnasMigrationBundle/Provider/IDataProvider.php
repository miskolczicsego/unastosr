<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 10:09
 */

namespace ShopMoves\UnasMigrationBundle\Provider;


interface IDataProvider
{
    public function getData();

    public function getFileUrl($fileName, $extension);
}