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

class ProductOptionDescriptionMigration extends BatchMigration
{

    protected $productOptionDescriptionsUri = 'productOptionDescriptions';

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
        foreach ($option as $opt) {
            $productOptionDescriptionData['name'] = $opt['name'];
            $productOptionDescriptionData['productOption']['id'] = $this
                ->productOptionDataProvider
                ->getProductOptionOuterId($opt['name'], $opt['productSku']);

            $productOptionDescriptionData['language']['id'] = $this->hungarianLanguageId;

            $this->addToBatchArray($this->productOptionDescriptionsUri, '' , $productOptionDescriptionData);
        }
    }
}