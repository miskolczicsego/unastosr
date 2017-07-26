<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 16:27
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Iterator\Provider\IDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductDescriptionMigration extends BatchMigration
{

    protected $productDescriptionUri = 'productDescriptions';

    protected $productDataProvider;

    public function __construct(
        ProductDataProvider $productDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->productDataProvider = $productDataProvider;
        parent::__construct($productDataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->productDataProvider->isProductDeleted($product)) {
            return;
        }

        $data['name'] = $product->Name;

        $data['shortDescription'] = isset($product->Description->Short) ?
            $product->Description->Short :
            '';

        $data['description'] = isset($product->Description->Long) ?
            $product->Description->Long :
            '';

        $data['packagingUnit'] = $product->Unit;

        if(isset($product->Meta)) {
            $data['metaKeywords'] = isset($product->Meta->Keywords) ? $product->Meta->Keywords : '';
            $data['metaDescription'] = isset($product->Meta->Description) ? $product->Meta->Description : '';
        }

        $data['product'] = [
            "id" => $this->productDataProvider->getProductOuterId($product->Sku)
        ];
        $data['language'] = [
            "id" => $this->productDataProvider->getSRHungarianLanguageId()
        ];

        $this->addToBatchArray($this->productDescriptionUri, '', $data);
    }
}