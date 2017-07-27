<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.07.
 * Time: 14:20
 */

namespace ShopMoves\UnasMigrationBundle\Category\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Category\Provider\CategoryDataProvider;
use ShopMoves\UnasMigrationBundle\Category\Provider\CategoryDescriptionDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryDescriptionMigration extends BatchMigration
{

    protected $categoryDescriptionUri = 'categoryDescriptions';

    public function __construct(
        CategoryDescriptionDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($categoryDescription)
    {
        $categoryDescriptionData['name'] = $categoryDescription['name'];
        $categoryDescriptionData['category']['id'] = $categoryDescription['categoryId'];
        $categoryDescriptionData['language'] = [
            "id" => $this->hungarianLanguageId
        ];

        $this->addToBatchArray($this->categoryDescriptionUri, '' , $categoryDescriptionData);
    }
}