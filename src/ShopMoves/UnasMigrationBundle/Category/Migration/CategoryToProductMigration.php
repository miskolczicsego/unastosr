<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.27.
 * Time: 10:21
 */

namespace ShopMoves\UnasMigrationBundle\Category\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Category\Provider\CategoryToProductDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryToProductMigration extends BatchMigration
{

    protected $productCategoryRelationsUri = 'productCategoryRelations';

    public function __construct(
        CategoryToProductDataProvider $categoryToProductDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        parent::__construct($categoryToProductDataProvider, $apiCall, $container);
    }

    public function process($categoryToProduct)
    {
        foreach ($categoryToProduct['categories'] as $category) {
            $productToCategoryData['product']['id'] = $categoryToProduct['productId'];
            $productToCategoryData['category']['id'] = $category['categoryId'];
            $this->addToBatchArray($this->productCategoryRelationsUri, '', $productToCategoryData);
        }




    }
}