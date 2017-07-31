<?php
namespace ShopMoves\UnasMigrationBundle\Product\Provider;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 15:30
 */
class ProductDataProvider extends DataProvider
{
    protected $fileName = 'kiscipoproduct';

    protected $extension = 'json';

    /**
     * ProductDataProvider constructor.
     * @param ContainerInterface $container
     */
    function __construct($container)
    {
        parent::__construct($container);
    }

    public function getData()
    {
        $json = $this->getFileContentAsJson();

        return $json->Products->Product;
    }

    public function isProductDeleted($product)
    {
        if ($product->State === 'deleted') {
            return true;
        }
        return false;
    }

    public function getParentProductId($product)
    {
        if(isset($product->Types) && $product->Types->Type === 'child') {
            return $this->getProductOuterId($product->Types->Parent);
        }
    }

    public function getProductPrice($productPrices)
    {
        foreach ($productPrices as $price) {
            if($price->Type == 'normal') {
                return $price->Net;
            }
        }
    }

    public function getProductQuantity($product)
    {
        $sum = 0;
        if (isset($product->Stocks->Stock)) {
            if (count($product->Stocks->Stock) == 1) {
                return $product->Stocks->Stock->Qty;
            } else {
                foreach ($product->Stocks->Stock as $productStock) {
                    $sum += $productStock->Qty;
                }
            }
        }
        return $sum;
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

    public function getUrlAliasToProduct($product)
    {
        $urlParts = explode('/', $product->Url);
        return $urlParts[count($urlParts) - 1];

    }

    public function getProductSpecialPrice($product)
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

    public function getProductDescriptionOuterId($product)
    {
        return base64_encode('product-productDescription=' . $product->Id);
    }

    public function getProductOuterId($sku)
    {
        return base64_encode('product-productSku=' . $sku);
    }

    public function getProductSpecialOuterId($sku)
    {
        return base64_encode('product-productSpecial=' . $sku);
    }
}