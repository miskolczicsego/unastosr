<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.06.
 * Time: 17:00
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductUrlAliasMigration extends BatchMigration
{
    protected $urlAliasesUri = 'urlAliases';

    protected $productDataProvider;

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

        $alias = $this->productDataProvider->getUrlAliasToProduct($product);
        $aliasOuterId = $this->productDataProvider->getUrlAliasOuterId($alias);

        $urlAliasData['id'] = $aliasOuterId;
        $urlAliasData['urlAlias'] = $alias;
        $urlAliasData['type'] = 'PRODUCT';
        $urlAliasData['urlAliasEntity']['id'] = $this->productDataProvider->getProductOuterId($product->Sku);

        $this->addToBatchArray($this->urlAliasesUri, $aliasOuterId, $urlAliasData);
    }


}