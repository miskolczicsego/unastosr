<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 17:03
 */

namespace ShopMoves\UnasMigrationBundle\Provider;


use ShopMoves\UnasMigrationBundle\Helper\LanguageHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DataProvider implements IDataProvider
{

    protected $fileName;

    protected $extension;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    protected $timeStamp;

    /**
     * @var LanguageHelper $languageHelper
     */
    protected $languageHelper;


    protected $hungarianLanguageId;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->languageHelper = $container->get('language_helper');
        $this->timeStamp = $container->get('timestamp_provider')->getTimestamp();
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
        unset($content);
        return $json;
    }

    public function getSRHungarianLanguageId()
    {
         $hungarianLanguageId = $this->languageHelper->getLanguageByKey('HU');

         return $hungarianLanguageId;
    }
}