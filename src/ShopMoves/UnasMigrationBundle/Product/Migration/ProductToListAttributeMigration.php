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
    public function __construct(
        ProductDataProvider $dataProvider,
        ListAttributeDataProvider $listAttributeDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container)
    {
        $this->listAttributeDataProvider = $listAttributeDataProvider;
        parent::__construct($dataProvider, $apiCall, $container);
    }


    public function process($product)
    {
        if ($this->isProductDeleted($product)) {
            return;
        }

        $values = $this->listAttributeDataProvider->getAttributeValueToProductBySku($product->Sku);
        $productOuterId = $this->getProductOuterId($product);
        if(!empty($values)) {
            foreach ($values as $value) {
                $listValueToProduct['product']['id'] = $productOuterId;
                $listValueToProduct['listAttributeValue']['id'] = $value['listAttributeValueId'];
                $this->addToBatchArray($this->productListAttributeValueRelationsUri, '' , $listValueToProduct);
            }
        }

    }


}