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
        foreach ($option as $opt) {
            $productOptionOuterId = $this
                ->productOptionDataProvider
                ->getProductOptionOuterId($opt['name'], $opt['productSku']);

            $productOptionData['id'] = $productOptionOuterId;
            $productOptionData['product']['id'] = $this->productDataProvider->getProductOuterId($opt['productSku']);

            $this->addToBatchArray($this->productOptionUri, $productOptionOuterId, $productOptionData);
        }
    }
}