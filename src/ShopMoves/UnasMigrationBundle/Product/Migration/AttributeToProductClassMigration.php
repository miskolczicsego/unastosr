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
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AttributeToProductClassMigration extends BatchMigration
{

    protected $productClassAttributeRelationUri = 'productClassAttributeRelations';

    protected $listAttributeMigration;

    protected $productClassMigration;

    public function __construct(
        ProductDataProvider $dataProvider,
        ListAttributeMigration $listAttributeMigration,
        ProductClassMigration $productClassMigration,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->listAttributeMigration = $listAttributeMigration;
        $this->productClassMigration = $productClassMigration;
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Params) || empty($product->Params)) {
            return;
        }

        $productClasses = $this->productClassMigration->getProductClasses();

        foreach ($productClasses as $class) {
            $this->buildAttributeClassRelations($class);
        }
    }

    public function buildAttributeClassRelations($class)
    {
        $attributeOuterIds = $this->listAttributeMigration->getAttributeIds();
        foreach ($attributeOuterIds as $attributeId) {

            $productClassToAttribute['attribute']['id'] = $attributeId;
            $productClassToAttribute['productClass']['id'] = $class['id'];

            $this->addToBatchArray($this->productClassAttributeRelationUri,'',  $productClassToAttribute);
        }
    }
}