<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.26.
 * Time: 13:53
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RelatedProductMigration extends BatchMigration
{
    protected $productDataProvider;

    protected $productRelatedProductRelationUri = 'productRelatedProductRelations';

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
        if ($this->productDataProvider->isProductDeleted($product) ||
            !isset($product->SimilarProducts) ||
            empty($product->SimilarProducts->SimilarProduct)
        ) {
            return;
        }

        $relatedProducts = $product->SimilarProducts->SimilarProduct;

        if (is_array($relatedProducts)) {

            foreach ($relatedProducts as $relatedProduct) {
                $this->builRelatedProductBatch($product->Sku, $relatedProduct);
            }
        } else {
            $this->builRelatedProductBatch($product->Sku, $relatedProducts);
        }
    }

    public function builRelatedProductBatch($sku, $relatedProduct)
    {
        $relatedProductData['product']['id'] = $this->productDataProvider->getProductOuterId($sku);
        $relatedProductData['relatedProduct']['id'] = $this->productDataProvider->getProductOuterId($relatedProduct->Sku);

        $this->addToBatchArray($this->productRelatedProductRelationUri, '' ,$relatedProductData);
    }
}