<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.17.
 * Time: 12:37
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductOptionMigration extends BatchMigration
{

    protected $productOptionUri = '/productOptions/';
    protected $productOptionValuesUri = '/productOptionValues/';
    protected $productOptionDescriptionsUri = '/productOptionDescriptions';
    protected $productOptionValueDescriptionsUri = '/productOptionValueDescriptions';


    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Variants) || empty($product->Variants)) {
            return;
        }

        if($product->Sku != '846197') {
            return;
        }

        if (is_array($product->Variants->Variant)) {
            //846197
            foreach ($product->Variants->Variant as $option) {
                $productOptionOuterId = $this->getProductOptionOuterId($option->Name, $product->Sku);
                $productOuterId = $this->getProductOuterId($product);

                $productOptionData['id'] = $productOptionOuterId;
                $productOptionData['product']['id'] = $productOuterId;

                $productOptionDescriptionData['name'] = $option->Name;
                $productOptionDescriptionData['productOption']['id'] = $productOptionOuterId;
                $productOptionDescriptionData['language']['id'] = $this->getHungarianLanguageResourceId();

                $this->addToBatchArray($this->productOptionUri, $productOptionOuterId, $productOptionData);
                $this->addToBatchArray($this->productOptionDescriptionsUri, '', $productOptionDescriptionData);

                if (is_array($option->Values->Value)) {
                    foreach ($option->Values->Value as $optionValue) {
                        $productOptionValueOuterId = $this->getProductOptionValueOuterId($optionValue->Name, $product->Sku);
                        $productOptionValueData['id'] = $productOptionValueOuterId;
                        $productOptionValueData['productOption']['id'] = $productOptionOuterId;

                        $productOptionValueDescriptionData['name'] = $optionValue->Name;
                        $productOptionValueDescriptionData['productOptionValue']['id'] = $productOptionValueOuterId;
                        $productOptionValueDescriptionData['language'] = [
                            'id' => $this->getHungarianLanguageResourceId()
                        ];
                        $this->addToBatchArray($this->productOptionValuesUri, $productOptionValueOuterId, $productOptionValueData);
                        $this->addToBatchArray($this->productOptionValueDescriptionsUri, '', $productOptionValueDescriptionData);
                    }
                } else {
                    //TODO: ha a value nem tömb
                }

            }
        } else {
            //TODO: ha a variant nem tömb
        }
    }

    public function getOuterId($data)
    {
    }

    public function getProductOptionOuterId($optionName, $productSku)
    {
        return base64_encode('product_product-option-outer-id=' . $optionName . '_' . $productSku);
    }

    public function getProductOptionDescriptionOuterId($optionName, $productSku)
    {
        return base64_encode('product_product-option-description-outer-id=' . $optionName . '_' . $productSku);
    }

    public function getProductOptionValueOuterId($optionValue, $productSku)
    {
        return base64_encode('product_product-option-value-outer-id=' . $optionValue . '_' . $productSku);
    }

}