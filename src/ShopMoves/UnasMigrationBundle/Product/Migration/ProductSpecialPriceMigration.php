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

    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {

        if ($this->isProductDeleted($product)) {
            return;
        }
        if (count($product->Prices->Price) >= 2) {
            $price = $this->getSpecialPrice($product);
        } else {
            return;
        }
        $outerId = $this->getOuterId($price[$product->Sku]);
        $data['id'] = $outerId;
        $data['price'] = $price[$product->Sku]['net'];
        $data['dateFrom'] = isset($price[$product->Sku]['start']) ? $price[$product->Sku]['start'] : '0000-00-00';
        $data['dateTo'] = isset($price[$product->Sku]['end']) ? $price[$product->Sku]['end'] : '0000-00-00';
        $data['product']['id'] = $this->getProductOuterId($product);

        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => 'http://demo.api.aurora.miskolczicsego/productSpecials/' . $outerId,
            'data' => $data
        ];
    }

    public function getSpecialPrice($product)
    {
        $special = [];
        foreach ($product->Prices->Price as $price) {
            if ($price->Type === 'sale') {
                $special[$product->Sku]['net'] = $price->Net;
                $special[$product->Sku]['sku'] = $product->Sku;
            }
            if(isset($price->Start)) {
                $special[$product->Sku]['start'] = $price->Start;
            }
            if(isset($price->End)) {
                $special[$product->Sku]['end'] = $price->End;
            }
        }

        return $special;
    }

    public function getOuterId($data)
    {
        return base64_encode($data['net'] . '_' . $data['sku']);
    }
}