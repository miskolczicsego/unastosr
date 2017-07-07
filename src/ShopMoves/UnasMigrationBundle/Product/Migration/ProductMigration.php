<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 15:42
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductMigration extends BatchMigration
{

    /**
     * ProductMigration constructor.
     * @param ProductDataProvider $dataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     */
    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    /**
     * @param $data
     */

    public function process($product)
    {

        if ($this->isProductDeleted($product)) {
            return;
        }
        $outerId = $this->getOuterId($product);
        $data['id'] = $outerId;
        $data['sku'] = $product->Sku;
        $data['status'] = $product->Statuses->Status->Value;
        $data['price'] = count($product->Prices->Price) == 1 ? $product->Prices->Price->Net : $this->getProductPrice($product->Prices->Price);
        $data['stock1'] = count($product->Stocks->Stock) == 1 ? $product->Stocks->Stock->Qty : $this->getProductQuantity($product->Stocks->Stock);
        $data['taxClass'] = [
            'id' => $this->container->get('tax_helper')->getTaxId($product->Prices->Vat)
        ];

//        dump($data);die;
        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => 'http://demo.api.aurora.miskolczicsego/products/' . $outerId,
            'data' => $data
        ];
    }

    public function getOuterId($product)
    {
        return base64_encode($product->Sku);
    }

    public function getProductPrice($productPrices)
    {
        foreach ($productPrices as $price) {
            if($price->Type == 'normal') {
                return $price->Net;
            }
        }
    }

    public function getProductQuantity($productStocks)
    {
        $sum = 0;
        foreach ($productStocks as $productStock) {
            $sum += $productStock->Qty;
        }

        return $sum;
    }
}