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

    /**
     * @var CsvToArray $csvToArray
     */
    protected $csvToArray;

    function __construct($container)
    {
        $this->container = $container;
        $this->csvToArray = $this->container->get('csv_to_array');
    }

    abstract public function _getData();

    public function getData()
    {
        $data = $this->_getData();

        return $data;
    }

    public function getCacheDir()
    {
        return $this->container->getParameter("kernel.cache_dir");
    }

    public function getFileUrl($fileName, $extension)
    {
        return $this->getCacheDir() . '/' . $fileName . '.' . $extension;
    }
}