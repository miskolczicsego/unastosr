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

    protected $categoryUri = '/categories/';

    protected $categoryDescriptionUri = '/categoryDescriptions/';

    public function __construct(CategoryDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Categories)) {
            return;
        }

        $hungarianLanguageId = 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ==';

        $category = $product->Categories->Category;

        if(is_array($category)) {
            foreach ($category as $cat) {
                if(!array_key_exists($cat->Id, $this->categoryIds)) {
                    $this->categoryIds[$cat->Id] = true;
                    $categoryParts = explode('|', $cat->Name);
                    if(count($categoryParts) > 1) {
                        $counter = 0;
                        foreach ($categoryParts as $categoryPart) {

                            $categoryOuterId = $this->getCategoryOuterId($categoryPart);
                            $categoryDescriptionOuterId = $this->getCategoryDescriptionOuterId($categoryPart);
                            $parentCategoryId = $this->getCategoryOuterId($categoryParts[$counter - 1]);

                            $data['id'] = $categoryOuterId;

                            if ($counter > 0) {
                                $data['parentCategory']['id'] = $parentCategoryId;
                            }

                            $descriptionData['id'] = $categoryDescriptionOuterId;
                            $descriptionData['name'] = $categoryPart;
                            $descriptionData['category']['id'] = $categoryOuterId;
                            $descriptionData['language'] = [
                                "id" => $hungarianLanguageId
                            ];


                            $this->addToBatchArray($this->categoryUri, $categoryOuterId, $data);
                            $this->addToBatchArray($this->categoryDescriptionUri, $categoryDescriptionOuterId, $descriptionData);

                            ++$counter;
                        }
                    } else {
                        $categoryIds[$cat->Id] = true;

                        $categoryOuterId = $this->getCategoryOuterId($cat->Name);
                        $categoryDescriptionOuterId = $this->getCategoryDescriptionOuterId($cat->Name);
                        $data['id'] = $categoryOuterId;

                        $descriptionData['id'] =  $categoryDescriptionOuterId;
                        $descriptionData['name'] = $cat->Name;
                        $descriptionData['category']['id'] = $categoryOuterId;
                        $descriptionData['language'] = [
                            "id" => $hungarianLanguageId
                        ];

                        $this->addToBatchArray($this->categoryUri, $categoryOuterId, $data);
                        $this->addToBatchArray($this->categoryDescriptionUri, $categoryDescriptionOuterId, $descriptionData);
                    }
                }
            }
        }
        if(!is_array($category)) {
            if(!array_key_exists($category->Id, $this->categoryIds)) {
                $this->categoryIds[$category->Id] = 1;
                $categoryParts = explode('|', $category->Name);
                if(count($categoryParts) > 1) {

                    $counter = 0;

                    foreach ($categoryParts as $categoryPart) {
                        $categoryOuterId = $this->getCategoryOuterId($categoryPart);
                        $categoryDescriptionOuterId = $this->getCategoryDescriptionOuterId($categoryPart);
                        $data['id'] = $categoryOuterId;

                        $descriptionData['id'] = $categoryDescriptionOuterId;
                        $descriptionData['name'] = $categoryPart;
                        $descriptionData['category']['id'] = $categoryOuterId;
                        $descriptionData['language'] = [
                            "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                        ];

                        if ($counter > 0) {
                            $parentCategoryOuterId = $this->getCategoryOuterId( $categoryParts[$counter-1]);
                            $data['parentCategory']['id'] = $parentCategoryOuterId;
                        }


                        $this->addToBatchArray($this->categoryUri, $categoryOuterId, $data);
                        $this->addToBatchArray($this->categoryDescriptionUri, $categoryDescriptionOuterId, $descriptionData);
                        ++$counter;
                    };
                } else {

                    $this->categoryIds[$category->Id] = 1;
                    $categoryOuterId = $this->getCategoryOuterId($category->Name);
                    $categoryDescriptionOuterId = $this->getCategoryDescriptionOuterId($category->Name);

                    $data['id'] = $categoryOuterId;

                    $descriptionData['id'] = $categoryDescriptionOuterId;
                    $descriptionData['name'] = $category->Name;
                    $descriptionData['category']['id'] =  $categoryOuterId;
                    $descriptionData['language'] = [
                        "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                    ];

                    $this->addToBatchArray($this->categoryUri, $categoryOuterId, $data);
                    $this->addToBatchArray($this->categoryDescriptionUri, $categoryDescriptionOuterId, $descriptionData);
                }
            }
        }
    }

    public function getOuterId($category)
    {
        return base64_encode($category->Id);
    }


    public function getCategoryOuterId($data)
    {
        return base64_encode('category_name-Category=' . $data);
    }

    public function getCategoryDescriptionOuterId($data)
    {
        return base64_encode('category_name-CategoryDescription=' . $data);
    }
}