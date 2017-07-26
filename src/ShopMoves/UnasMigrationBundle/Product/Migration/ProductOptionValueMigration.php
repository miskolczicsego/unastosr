<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.26.
 * Time: 15:33
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductOptionDataProvider;
use ShopMoves\UnasMigrationBundle\Provider\IDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductOptionValueMigration extends BatchMigration
{

    /**
     * @var ProductOptionDataProvider $productOptionDataProvider
     */
    protected $productOptionDataProvider;

    protected $productOptionValuesUri = 'productOptionValues';


    public function __construct(
        ProductOptionDataProvider $productOptionDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->productOptionDataProvider = $productOptionDataProvider;
        parent::__construct($productOptionDataProvider, $apiCall, $container);
    }

    public function process($option)
    {
        if (is_array($option['values'])) {
            foreach ($option['values'] as $optionValue) {

                $this->buildOptionValueBatch($optionValue, $option);
            }
        } else {
            $this->buildOptionValueBatch($option['values'], $option);
        }

    }

    public function buildOptionValueBatch($optionValue, $option)
    {
        $productOptionValueOuterId = $this
            ->productOptionDataProvider
            ->getProductOptionValueOuterId($optionValue->Name, $option['productSku']);

        $productOptionOuterId = $this
            ->productOptionDataProvider
            ->getProductOptionOuterId($option['name'], $option['productSku']);


        $productOptionValueData['id'] = $productOptionValueOuterId;
        $productOptionValueData['productOption']['id'] = $productOptionOuterId;

        $this->addToBatchArray($this->productOptionValuesUri, $productOptionValueOuterId, $productOptionValueData);
    }
}