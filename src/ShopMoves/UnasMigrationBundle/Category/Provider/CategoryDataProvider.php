<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.05.
 * Time: 13:35
 */

namespace ShopMoves\UnasMigrationBundle\Category\Provider;


use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryDataProvider extends DataProvider
{
    protected $fileName = '1501662054503product';

    protected $extension = 'json';

    protected $categoryData = [];

    protected $categoryProductRelationData = [];

    protected $productDataProvider;

    function __construct(
        ContainerInterface $container,
        ProductDataProvider $productDataProvider
    ) {
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

          $category = $product->Categories->Category;
          if(is_array($category)) {
              foreach ($category as $cat) {
                  $this->gatherCategoryData($cat);
              }
          } else {
              $this->gatherCategoryData($category);
          }
      }
      return $this->categoryData;

    }

    public function gatherCategoryData($cat)
    {
        if (!array_key_exists($cat->Id, $this->categoryData)) {
            $this->categoryData[$cat->Id] = [
                'categoryName' => $cat->Name,
                'categoryId' => $cat->Id
            ];
        }
    }

    public function getCategoryOuterId($categoryName)
    {
        return base64_encode('category-CategoryName=' . $categoryName);
    }
}