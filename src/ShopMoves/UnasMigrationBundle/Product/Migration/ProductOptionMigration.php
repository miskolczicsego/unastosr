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
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductOptionDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductOptionMigration extends BatchMigration
{

    protected $productOptionUri = 'productOptions';

    /**
     * @var ProductDataProvider $productOptionDataProvider
     */
    protected $productDataProvider;

    /**
     * @var ProductOptionDataProvider $productOptionDataProvider
     */
    protected $productOptionDataProvider;


    public function __construct(
        ProductOptionDataProvider $productOptionDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container,
        ProductDataProvider $productDataProvider
    ) {
        $this->productOptionDataProvider = $productOptionDataProvider;
        $this->productDataProvider = $productDataProvider;
        parent::__construct($productOptionDataProvider, $apiCall, $container);
    }

    public function process($option)
    {
        $productOptionOuterId = $this
            ->productOptionDataProvider
            ->getProductOptionOuterId($option['name'], $option['productSku']);

        $productOptionData['id'] = $productOptionOuterId;
        $productOptionData['product']['id'] = $this->productDataProvider->getProductOuterId($option['productSku']);

        $this->addToBatchArray($this->productOptionUri, $productOptionOuterId, $productOptionData);

    }

//    public function buildOptionValueAndDescription($optionValue, $product)
//    {
//
//        $productOptionValueDescriptionData['id'] = $productOptionValueDescriptionOuterId;
//        $productOptionValueDescriptionData['name'] = $optionValue->Name;
//        $productOptionValueDescriptionData['productOptionValue']['id'] = $productOptionValueOuterId;
//        $productOptionValueDescriptionData['language'] = [
//            'id' => $this->getHungarianLanguageResourceId()
//        ];
//        $this->addToBatchArray($this->productOptionValuesUri, $productOptionValueOuterId, $productOptionValueData);
//        $this->addToBatchArray($this->productOptionValueDescriptionsUri, $productOptionValueDescriptionOuterId, $productOptionValueDescriptionData);
//    }
}