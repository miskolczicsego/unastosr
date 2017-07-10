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

    protected $productUri = '/products/';

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
     * @param $product
     */

    public function process($product)
    {

        if ($this->isProductDeleted($product)) {
            return;
        }
        $outerId = $this->getProductOuterId($product);
        $data['id'] = $outerId;
        $data['sku'] = $product->Sku;
        $data['status'] = $product->Statuses->Status->Value;
        $data['price'] = count($product->Prices->Price) == 1 ? $product->Prices->Price->Net : $this->getProductPrice($product->Prices->Price);
        $data['stock1'] = count($product->Stocks->Stock) == 1 ? $product->Stocks->Stock->Qty : $this->getProductQuantity($product->Stocks->Stock);
        $data['taxClass'] = [
            'id' => $this->container->get('tax_helper')->getTaxId($product->Prices->Vat)
        ];
        $data['parentProduct']['id'] = $this->getParentProduct($product);
        $data['productClass']['id'] = $this->getProductClassId($product);

        $this->addToBatchArray($this->productUri, $outerId, $data);
    }

    public function getOuterId($product)
    {
//        return base64_encode($product->Sku);/
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

    public function getParentProduct($product)
    {
        if(isset($product->Types) && $product->Types->Type === 'child') {
            return base64_encode('product_id-Product=' . $product->Types->Parent);
        }
    }

    public function getProductClassId($product)
    {
//        dump($product->Datas);die;
        if (isset($product->Datas) && is_array($product->Datas->Data)) {
            $class = array_pop($product->Datas->Data);
            return base64_encode('product_class-Product=' . $class->Value);
        } else {
            if (isset($product->Datas) && $product->Datas->Data->Name === 'Típus') {
                return base64_encode('product_class-Product=' . $product->Datas->Data->Value);
            }
        }
        return '';
    }
}