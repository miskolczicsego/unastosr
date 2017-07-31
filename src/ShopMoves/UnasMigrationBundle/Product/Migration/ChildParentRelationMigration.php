<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.31.
 * Time: 13:43
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChildParentRelationMigration extends BatchMigration
{

    protected $productDataProvider;

    protected $productUri = 'products';

    public function __construct(
        ProductDataProvider $productDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->productDataProvider = $productDataProvider;
        parent::__construct($productDataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->productDataProvider->isProductDeleted($product)) {
            return;
        }

        $productOuterId = $this->productDataProvider->getProductOuterId($product->Sku);
        $productData['id'] = $productOuterId;
        $productData['sku'] = $product->Sku;
        $productData['parentProduct']['id'] = $this->productDataProvider->getParentProductId($product);

        $this->addToBatchArray($this->productUri, $productOuterId, $productData);
    }
}