<?php
namespace ShopMoves\UnasMigrationBundle\Product\Provider;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 15:30
 */
class ProductDataProvider extends DataProvider
{
    protected $fileName = '1500283059673product';

    protected $extension = 'json';

    /**
     * ProductDataProvider constructor.
     * @param ContainerInterface $container
     */
    function __construct($container)
    {
        parent::__construct($container);
    }

    public function _getData()
    {
        $fileUrl = $this->getFileUrl($this->fileName, $this->extension);
        $content = file_get_contents($fileUrl);

        $json = json_decode($content);

        return $json->Products->Product;
    }
}