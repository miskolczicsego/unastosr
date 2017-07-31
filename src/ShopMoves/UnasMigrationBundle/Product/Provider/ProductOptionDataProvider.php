<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.26.
 * Time: 15:48
 */

namespace ShopMoves\UnasMigrationBundle\Product\Provider;


use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductOptionDataProvider extends DataProvider
{

    protected $fileName = 'kiscipoproduct';

    protected $extension = 'json';

    protected $optionDatas = [];
    /**
     * @var ProductDataProvider $productDataProvider
     */
    protected $productDataProvider;

    function __construct(
        ContainerInterface $container,
        ProductDataProvider $productDataProvider
    ) {
        $this->productDataProvider = $productDataProvider;
        parent::__construct($container);
    }

    public function getData()
    {
        $products = $this->getFileContentAsJson();

        foreach ($products->Products->Product as $product) {
            if ($this->productDataProvider->isProductDeleted($product) ||
                !isset($product->Variants) ||
                empty($product->Variants)
            ) {
                continue;
            }

            $options = $product->Variants->Variant;

            if (is_array($options)) {
                foreach ($options as $option) {
                    $this->gatherOptionDatasToProduct($option, $product);
                }
            } else {
                $this->gatherOptionDatasToProduct($options, $product);
            }
        }

        return $this->optionDatas;

    }

    public function gatherOptionDatasToProduct($option, $product)
    {
        $this->optionDatas[$product->Sku] = [
            'name' => $option->Name,
            'productSku' => $product->Sku,
            'values' => $this->collectValuesToOption($option->Values->Value)
        ];
    }

    public function getOptionDatas()
    {
        return $this->optionDatas;
    }

    public function collectValuesToOption($optionValues)
    {
        $data = [];
        if (is_array($optionValues)) {
            foreach ($optionValues as $optionValue) {
                $data[] = $optionValue;
            }
        } else {
            $data[] = $optionValues;
        }

        return $data;
    }

    public function getOptionDatasBySku($sku) {
        return $this->optionDatas[$sku];
    }

    public function getProductOptionOuterId($optionName, $sku)
    {
        return base64_encode('product-productOption=' . $optionName . '|' . $sku);
    }


    public function getProductOptionValueOuterId($optionValue, $sku)
    {
        return base64_encode('product-productOptionValue=' . $optionValue  . '|' . $sku);
    }
}