<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.27.
 * Time: 12:15
 */

namespace ShopMoves\UnasMigrationBundle\Category\Provider;


use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryToProductDataProvider extends DataProvider
{

    protected $fileName = 'kiscipoproduct';

    protected $extension = 'json';

    protected $categoryDataProvider;

    protected $productDataProvider;

    protected $categoryToProductDatas = [];

    function __construct(
        ContainerInterface $container,
        ProductDataProvider $productDataProvider,
        CategoryDataProvider $categoryDataProvider
    ) {
        $this->categoryDataProvider = $categoryDataProvider;
        $this->productDataProvider = $productDataProvider;
        parent::__construct($container);
    }

    public function getData()
    {
        $json = $this->getFileContentAsJson();

        $products = $json->Products->Product;

        foreach ($products as $product) {

            if ($this->productDataProvider->isProductDeleted($product) ||
                !isset($product->Categories)
            ) {
                continue;
            }
            $this->gatherCategoryToProductData($product);
        }


        return $this->categoryToProductDatas;
    }

    public function gatherCategoryToProductData($product)
    {
        $categories = $product->Categories->Category;
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $this->buildDataArray($category, $product);
            }
        } else {
            $this->buildDataArray($categories, $product);
        }
    }

    public function buildDataArray($category, $product)
    {
        $categoryParts = explode('|', $category->Name);
        if (count($categoryParts) > 1) {
            $queue = [];
            foreach ($categoryParts as $categoryPart) {
                array_push($queue, $categoryPart);

                $categoryOuterId = $this
                    ->categoryDataProvider
                    ->getCategoryOuterId(implode('|', $queue));

                $this->categoryToProductDatas[$product->Sku]['productId'] = $this->productDataProvider->getProductOuterId($product->Sku);


                $this->categoryToProductDatas[$product->Sku]['categories'][] = [
                    'categoryId' => $categoryOuterId,
                ];

            }
        } else {
            $categoryOuterId = $this->categoryDataProvider->getCategoryOuterId($category->Name);
            $productOuterId = $this->productDataProvider->getProductOuterId($product->Sku);

            $this->categoryToProductDatas[$product->Sku] = [
                'productId' => $productOuterId,
                'categories' =>
                    [
                        [
                            'categoryId' => $categoryOuterId,
                        ]
                    ]
            ];

        }
    }

}