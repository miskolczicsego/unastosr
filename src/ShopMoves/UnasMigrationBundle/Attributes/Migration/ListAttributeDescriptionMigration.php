<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.25.
 * Time: 12:33
 */

namespace ShopMoves\UnasMigrationBundle\Attributes\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListAttributeDescriptionMigration extends BatchMigration
{

    protected $attributeDescriptionsUri = 'attributeDescriptions';

    public function __construct(
        ListAttributeDataProvider $listAttributeDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        parent::__construct($listAttributeDataProvider, $apiCall, $container);
    }

    public function process($listAttribute)
    {
        $attributeDescriptionData['name'] = $listAttribute['name'];
        $attributeDescriptionData['language']['id'] = $this->hungarianLanguageId;
        $attributeDescriptionData['attribute']['id'] = $listAttribute['outerId'];

        $this->addToBatchArray($this->attributeDescriptionsUri, '', $attributeDescriptionData);

    }
}