<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.25.
 * Time: 16:16
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Migration\ListAttributeMigration;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductClassDataProvider;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AttributeToProductClassMigration extends BatchMigration
{

    protected $productClassAttributeRelationUri = 'productClassAttributeRelations';

    protected $listAttributeMigration;

    /**
     * @var ProductClassMigration $productClassMigration
     */
    protected $productClassMigration;

    protected $productDataProvider;
    protected $productClassDataProvider;

    public function __construct(
        ProductClassDataProvider $productClassDataProvider,
        ListAttributeMigration $listAttributeMigration,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->listAttributeMigration = $listAttributeMigration;
        $this->productClassDataProvider = $productClassDataProvider;
        parent::__construct($productClassDataProvider, $apiCall, $container);
    }

    public function process($productClasses)
    {
        foreach ($productClasses as $class) {
            $this->buildAttributeClassRelations($class);
        }
    }

    public function buildAttributeClassRelations($class)
    {
        $attributeOuterIds = $this->listAttributeMigration->getListAttributeIds();
        foreach ($attributeOuterIds as $attributeId) {

            $productClassToAttribute['attribute']['id'] = $attributeId;
            $productClassToAttribute['productClass']['id'] = $this->productClassDataProvider->getProductClassOuterId($class);

            $this->addToBatchArray($this->productClassAttributeRelationUri,'',  $productClassToAttribute);
        }
    }
}