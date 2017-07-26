<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.25.
 * Time: 13:15
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use ShopMoves\UnasMigrationBundle\Provider\IDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductToListAttributeMigration extends BatchMigration
{

    protected $productListAttributeValueRelationsUri = 'productListAttributeValueRelations';
    /**
     * @var ListAttributeDataProvider $listAttributeDataProvider
     */
    protected $listAttributeDataProvider;

    /**
     * @var ProductDataProvider $productDataProvider
     */
    protected $productDataProvider;


    public function __construct(
        ProductDataProvider $productDataProvider,
        ListAttributeDataProvider $listAttributeDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container)
    {
        $this->listAttributeDataProvider = $listAttributeDataProvider;
        $this->productDataProvider = $productDataProvider;
        parent::__construct($productDataProvider, $apiCall, $container);
    }


    public function process($product)
    {
        if ($this->productDataProvider->isProductDeleted($product)) {
            return;
        }

        $values = $this->listAttributeDataProvider->getAttributeValueToProductBySku($product->Sku);
        $productOuterId = $this->productDataProvider->getProductOuterId($product->Sku);
        if(!empty($values)) {
            foreach ($values as $value) {
                $listValueToProduct['product']['id'] = $productOuterId;
                $listValueToProduct['listAttributeValue']['id'] = $value['listAttributeValueId'];
                $this->addToBatchArray($this->productListAttributeValueRelationsUri, '' , $listValueToProduct);
            }
        }

    }


}