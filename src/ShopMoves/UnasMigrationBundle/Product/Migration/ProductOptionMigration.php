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

    protected $productOptionUri = 'productOptions';
    protected $productOptionValuesUri = 'productOptionValues';
    protected $productOptionDescriptionsUri = 'productOptionDescriptions';
    protected $productOptionValueDescriptionsUri = 'productOptionValueDescriptions';

    protected $productOptionOuterId;

    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Variants) || empty($product->Variants)) {
            return;
        }

        $options = $product->Variants->Variant;

        //TODO: esetleg sratégiába ki lehetne rakni
        if (is_array($options)) {
            foreach ($options as $option) {
                $this->buildOptionAndDescription($option, $product);
                $optionValues = $option->Values->Value;
                if (is_array($optionValues)) {
                    foreach ($optionValues as $optionValue) {
                       $this->buildOptionValueAndDescription($optionValue, $product);
                    }
                } else {
                    $this->buildOptionValueAndDescription($optionValues, $product);
                }

            }
        } else {
            $this->buildOptionAndDescription($options, $product);
            $optionValues = $options->Values->Value;
            if (is_array($optionValues)) {
                foreach ($optionValues as $optionValue) {
                   $this->buildOptionValueAndDescription($optionValue, $product);
                }
            } else {
                $this->buildOptionValueAndDescription($optionValues, $product);
            }
        }
    }
    public function buildOptionAndDescription($option, $product)
    {
        $this->productOptionOuterId = $this->getProductOptionOuterId($option->Name, $product->Sku);
        $productOuterId = $this->getProductOuterId($product);

        $productOptionData['id'] = $this->productOptionOuterId;
        $productOptionData['product']['id'] = $productOuterId;

        $productOptionDescriptionData['name'] = $option->Name;
        $productOptionDescriptionData['productOption']['id'] = $this->productOptionOuterId;
        $productOptionDescriptionData['language']['id'] = $this->getHungarianLanguageResourceId();

        $this->addToBatchArray($this->productOptionUri, $this->productOptionOuterId, $productOptionData);
        $this->addToBatchArray($this->productOptionDescriptionsUri, '', $productOptionDescriptionData);
    }

    public function buildOptionValueAndDescription($optionValue, $product)
    {
        $productOptionValueDescriptionOuterId = $this->getProductOptionValueDescriptionOuterId($optionValue->Name, $product->Sku);
        $productOptionValueOuterId = $this->getProductOptionValueOuterId($optionValue->Name, $product->Sku);

        $productOptionValueData['id'] = $productOptionValueOuterId;
        $productOptionValueData['productOption']['id'] = $this->productOptionOuterId;

        $productOptionValueDescriptionData['id'] = $productOptionValueDescriptionOuterId;
        $productOptionValueDescriptionData['name'] = $optionValue->Name;
        $productOptionValueDescriptionData['productOptionValue']['id'] = $productOptionValueOuterId;
        $productOptionValueDescriptionData['language'] = [
            'id' => $this->getHungarianLanguageResourceId()
        ];
        $this->addToBatchArray($this->productOptionValuesUri, $productOptionValueOuterId, $productOptionValueData);
        $this->addToBatchArray($this->productOptionValueDescriptionsUri, $productOptionValueDescriptionOuterId, $productOptionValueDescriptionData);
    }
    public function getOuterId($data)
    {
    }

    public function getProductOptionOuterId($optionName, $productSku)
    {
        return base64_encode('product_product-option-outer-id=' . $optionName . '_' . $productSku. $this->timeStamp);
    }

    public function getProductOptionDescriptionOuterId($optionName, $productSku)
    {
        return base64_encode('product_product-option-description-outer-id=' . $optionName . '_' . $productSku. $this->timeStamp);
    }

    public function getProductOptionValueOuterId($optionValue, $productSku)
    {
        return base64_encode('product_product-option-value-outer-id=' . $optionValue . '_' . $productSku. $this->timeStamp);
    }

    public function getProductOptionValueDescriptionOuterId($optionValueName, $productSku)
    {
        return base64_encode('product_product-option-value-description-outer-id=' . $optionValueName . '_' . $productSku. $this->timeStamp);
    }

}