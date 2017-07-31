<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.27.
 * Time: 11:05
 */

namespace ShopMoves\UnasMigrationBundle\Category\Provider;


use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryDescriptionDataProvider extends DataProvider
{
    protected $fileName = 'pipereproduct';

    protected $extension = 'json';

    protected $categoryDescriptionData = [];

    protected $productDataProvider;

    protected $categoryDataProvider;

    protected $categoryIds = [];

    function __construct(
        ContainerInterface $container,
        ProductDataProvider $productDataProvider,
        CategoryDataProvider $categoryDataProvider
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->categoryDataProvider = $categoryDataProvider;
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

            $category = $product->Categories->Category;
            if(is_array($category)) {
                foreach ($category as $cat) {
                    $this->gatherCategoryDescriptionData($cat);
                }
            } else {
                $this->gatherCategoryDescriptionData($category);
            }
        }

        return $this->categoryDescriptionData;
    }

    public function gatherCategoryDescriptionData($category)
    {
        if (!array_key_exists($category->Id, $this->categoryIds)) {
            $categoryParts = explode('|', $category->Name);
            if(count($categoryParts) > 1) {
                $counter = 0;
                $queue = [];
                foreach ($categoryParts as $categoryPart) {
                    array_push($queue, $categoryPart);

                    $categoryOuterId = $this
                        ->categoryDataProvider
                        ->getCategoryOuterId(implode('|', $queue));

                    $this->categoryDescriptionData[implode('|', $queue)] = [
                        'categoryId' => $categoryOuterId,
                        'name' => $categoryPart
                    ];

                    ++$counter;
                }
            } else {
                $categoryOuterId = $this->categoryDataProvider->getCategoryOuterId($category->Name);
                $this->categoryDescriptionData[$category->Name] = [
                    'categoryId' => $categoryOuterId,
                    'name' => $category->Name,
                ];
            }
        }
    }
}