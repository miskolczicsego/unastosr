<?php

namespace ShopMoves\UnasMigrationBundle\Category\Migration;

use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Category\Provider\CategoryDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.05.
 * Time: 13:47
 */
class CategoryMigration extends BatchMigration
{

    protected $categoryUri = 'categories';

    protected $categoryDataProvider;

    public function __construct(
        CategoryDataProvider $categoryDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container)
    {
        $this->categoryDataProvider = $categoryDataProvider;
        parent::__construct($categoryDataProvider, $apiCall, $container);
    }

    public function process($category)
    {
        $this->buildCategoryBatch($category);
    }

    public function buildCategoryBatch($category)
    {
        $categoryParts = explode('|', $category['categoryName']);
        if(count($categoryParts) > 1) {
            $counter = 0;
            $queue = [];
            foreach ($categoryParts as $categoryPart) {
                $parentCategoryString = implode('|', $queue);
                array_push($queue, $categoryPart);

                $categoryOuterId = $this
                    ->categoryDataProvider
                    ->getCategoryOuterId(implode('|', $queue));

                if ($counter > 0) {
                    $categoryData['parentCategory']['id'] = $this
                        ->categoryDataProvider
                        ->getCategoryOuterId($parentCategoryString);
                }
                $categoryData['id'] = $categoryOuterId;
                $this->addToBatchArray($this->categoryUri, $categoryOuterId, $categoryData);

                ++$counter;
            }
        } else {
            $categoryOuterId = $this->categoryDataProvider->getCategoryOuterId($category['categoryName']);
            $categoryData['id'] = $categoryOuterId;
            $this->addToBatchArray($this->categoryUri, $categoryOuterId, $categoryData);
        }
    }
}