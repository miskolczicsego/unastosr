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
     * @var ProductDataProvider
     */
    protected $productDataProvider;


    protected $listAttributeDataProvider;

    /**
     * ProductClassMigration constructor.
     * @param ProductDataProvider $productDataProvider
     * @param ListAttributeDataProvider $listAttributeDataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     */
    public function __construct(
        ProductDataProvider $productDataProvider,
        ListAttributeDataProvider $listAttributeDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->listAttributeDataProvider=$listAttributeDataProvider;
        parent::__construct($productDataProvider, $apiCall, $container);
    }

    /**
     * @param object $product
     */
    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Params) || empty($product->Params)) {
            return;
        }

        $classes = $product->Params->Param;

        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->buildClassBatch($class);
            }
        } else {
            $this->buildClassBatch($classes);
        }
    }

    public function buildClassBatch($class)
    {
        if(!array_key_exists($class->Name, $this->productClasses)) {
            $outerId = $this->productDataProvider->getProductClassOuterId($class);
            $this->productClasses[$class->Name] = [
                'id' => $outerId
            ];
            $data['id'] = $outerId;
            $data['name'] = 'Változó ' . $class->Name . ' szerint';
            $data['firstVariantSelectType'] = 'LIST';
            $data['firstVariantParameter']['id'] = $this->listAttributeDataProvider->getListAttributeOuterId(Transliterator::transliterate($class->Name, '_'));
            $this->addToBatchArray($this->productClassUri, $outerId, $data);
        }
    }

    public function getProductClasses()
    {
        return $this->productClasses;
    }
}