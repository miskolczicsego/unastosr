<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 17:03
 */

namespace ShopMoves\UnasMigrationBundle\Provider;

use ShopMoves\UnasMigrationBundle\Utils\CsvToArray;

abstract class DataProvider implements IDataProvider
{

    protected $fileName;

    protected $extension;

    protected $container;


    function __construct($container)
    {
        $this->container = $container;
    }

    abstract public function getData();


    public function getCacheDir()
    {
        return $this->container->getParameter("kernel.cache_dir");
    }

    public function getFileUrl($fileName, $extension)
    {
        return $this->getCacheDir() . '/' . $fileName . '.' . $extension;
    }

    public function getFileContentAsJson()
    {
        $fileUrl = $this->getFileUrl($this->fileName, $this->extension);
        $content = file_get_contents($fileUrl);
        $json = json_decode($content);

        return $json;
    }
}