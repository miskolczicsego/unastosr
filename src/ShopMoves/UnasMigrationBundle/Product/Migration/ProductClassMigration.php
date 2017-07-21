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
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductClassMigration extends BatchMigration
{

    protected $productClassUri = 'productClasses';

    protected $productClassAttributeRelationUri = 'productClassAttributeRelations';

    protected $productClasses = [];

    protected $listAttributeMigration;

    public function __construct(
        ProductDataProvider $dataProvider,
        ListAttributeMigration $listAttributeMigration,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->listAttributeMigration = $listAttributeMigration;
        parent::__construct($dataProvider, $apiCall, $container);
    }
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

    public function getOuterId($class)
    {
        return base64_encode('product-Product-Class=' . $class->Id);
    }

    public function buildClassBatch($class)
    {
        if(!array_key_exists($class->Name, $this->productClasses)) {
            $this->productClasses[$class->Name] = true;
            $outerId = $this->getOuterId($class);
            $data['id'] = $outerId;
            $data['name'] = 'Változó ' . $class->Name . ' szerint';
            $data['firstVariantSelectType'] = 'LIST';
            $data['firstVariantParameter']['id'] = base64_encode('product_listAttribute=' . Transliterator::transliterate($class->Name, '_'));
            $this->addToBatchArray($this->productClassUri, $outerId, $data);

            $attributeOuterIds = $this->listAttributeMigration->getAttributeIds();
//            dump($attributeOuterIds);die;
            foreach ($attributeOuterIds as $attributeId) {
                $productClassToAttribute['attribute']['id'] = $attributeId;
                $productClassToAttribute['productClass']['id'] = $outerId;

                $this->addToBatchArray($this->productClassAttributeRelationUri, '', $productClassToAttribute);
            }
        }
    }
}