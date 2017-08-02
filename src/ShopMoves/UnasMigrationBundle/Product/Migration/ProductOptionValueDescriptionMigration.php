<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.26.
 * Time: 15:34
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductOptionDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductOptionValueDescriptionMigration extends BatchMigration
{

    /**
     * @var ProductOptionDataProvider $productOptionDataProvider
     */
    protected $productOptionDataProvider;

    protected $productOptionValueDescriptionsUri = 'productOptionValueDescriptions';

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
        foreach ($option as $opt) {
            if (is_array($opt['values'])) {
                foreach ($opt['values'] as $optionValue) {

                    $this->buildOptionValueDescriptionBatch($optionValue, $opt);
                }
            } else {
                $this->buildOptionValueDescriptionBatch($opt['values'], $opt);
            }
        }
    }

    public function buildOptionValueDescriptionBatch($optionValue, $option)
    {
        $productOptionValueDescriptionData['name'] = $optionValue->Name;
        $productOptionValueDescriptionData['productOptionValue']['id'] = $this
        ->productOptionDataProvider
        ->getProductOptionValueOuterId($optionValue->Name, $option['productSku']);

        $productOptionValueDescriptionData['language'] = [
            'id' => $this->hungarianLanguageId
        ];

        $this->addToBatchArray($this->productOptionValueDescriptionsUri, '', $productOptionValueDescriptionData);
    }
}