<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 15:42
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductMigration extends BatchMigration
{

    protected $productUri = 'products';


    protected $mainImageToProduct;

    /**
     * ProductMigration constructor.
     * @param ProductDataProvider $dataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     */
    public function __construct(
        ProductDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {

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

        $unasStatus = $product->Statuses->Status->Value;
        $srStatus = ($unasStatus == '1' || $unasStatus == '2' || $unasStatus == '3') ? '1' : '0';
        $productOuterId = $this->getProductOuterId($product);
        $data['id'] = $productOuterId;
        $data['sku'] = $product->Sku;
        $data['status'] = $srStatus;
        if ($unasStatus == '3') {
            $data['orderable'] = 0;
        }
        $data['price'] = count($product->Prices->Price) == 1 ? $product->Prices->Price->Net : $this->getProductPrice($product->Prices->Price);
        $data['stock1'] = count($product->Stocks->Stock) == 1 ? $product->Stocks->Stock->Qty : $this->getProductQuantity($product->Stocks->Stock);
        $data['taxClass'] = [
            'id' => $this->container->get('tax_helper')->getTaxId($product->Prices->Vat)
        ];
        $data['mainPicture'] = $this->getMainPictureToProduct($product);
        $data['parentProduct']['id'] = $this->getParentProduct($product);
        $data['productClass']['id'] = $this->getProductClassId($product);

        $this->addToBatchArray($this->productUri, $productOuterId, $data);
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
            return base64_encode('product_id-Product=' . $product->Types->Parent . $this->timeStamp);
        }
    }


    public function getProductClassId($product)
    {
//        dump($product->Datas);die;
        if (isset($product->Params) && is_array($product->Params->Param)) {
            $class = array_pop($product->Params->Param);
            return base64_encode('product-Product-Class=' . $class->Id . $this->timeStamp);
        } elseif (isset($product->Params) && !is_array($product->Params->Param)) {
                return base64_encode('product-Product-Class=' . $product->Params->Param->Id . $this->timeStamp);
        }
        return '';
    }

    public function getMainPictureToProduct($product)
    {
        if (!isset($product->Images)) {
            return '';
        }
        $image = $product->Images->Image;
        if (is_array($image)) {
            $path = 'product/' . basename($image[0]->Url->Medium);
        } else {
            $path = 'product/' . basename($image->Url->Medium);
        }

        return $path;
    }

}