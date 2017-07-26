<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.06.
 * Time: 16:15
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductSpecialPriceMigration extends BatchMigration
{

    protected $specialPriceUri = 'productSpecials';

    /**
     * @var ProductDataProvider $productDataProvider
     */
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
        if (count($product->Prices->Price) >= 2) {
            $price = $this->productDataProvider->getProductSpecialPrice($product);
        } else {
            return;
        }
        $data['price'] = $price[$product->Sku]['net'];

        $data['dateFrom'] = isset($price[$product->Sku]['start']) ?
            $price[$product->Sku]['start'] : '0000-00-00';

        $data['dateTo'] = isset($price[$product->Sku]['end']) ?
            $price[$product->Sku]['end'] : '0000-00-00';

        $data['product']['id'] = $this->productDataProvider->getProductOuterId($product->Sku);

        $this->addToBatchArray($this->specialPriceUri, '', $data);
    }
}