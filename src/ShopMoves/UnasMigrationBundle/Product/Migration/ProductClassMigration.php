<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.10.
 * Time: 13:23
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use Behat\Transliterator\Transliterator;
use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Migration\ListAttributeMigration;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductClassDataProvider;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductClassMigration extends BatchMigration
{

    protected $productClassUri = 'productClasses';


    /**
     * @var array $productClasses
     */
    protected $productClasses = [];

    /**
     * @var ProductClassDataProvider
     */
    protected $productClassDataProvider;


    protected $listAttributeDataProvider;

    /**
     * ProductClassMigration constructor.
     * @param ProductClassDataProvider $productClassDataProvider
     * @param ListAttributeDataProvider $listAttributeDataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     */
    public function __construct(
        ProductClassDataProvider $productClassDataProvider,
        ListAttributeDataProvider $listAttributeDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->listAttributeDataProvider = $listAttributeDataProvider;
        $this->productClassDataProvider = $productClassDataProvider;
        parent::__construct($productClassDataProvider, $apiCall, $container);
    }

    /**
     * @param array $class
     */
    public function process($class)
    {
        $this->buildClassBatch($class);
    }

    public function buildClassBatch($class)
    {
            $productClassOuterId = $this->productClassDataProvider->getProductClassOuterId($class['name']);

            $productClassData['id'] = $productClassOuterId;
            if ($class['name'] === 'Nincs terméktípus') {
                $productClassData['name'] = $class['name'];
            } else {
                $productClassData['name'] = 'Változó ' . $class['name'] . ' szerint';
                $productClassData['firstVariantSelectType'] = 'LIST';

                $productClassData['firstVariantParameter']['id'] = $this->listAttributeDataProvider->getListAttributeOuterId(
                    Transliterator::transliterate($class['name'], '_')
                );
            }

            $this->addToBatchArray($this->productClassUri, $productClassOuterId, $productClassData);

    }


}