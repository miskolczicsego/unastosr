<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.25.
 * Time: 13:02
 */

namespace ShopMoves\UnasMigrationBundle\Attributes\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListAttributeValueDescriptionMigration extends BatchMigration
{
    /**
     * @var string $listAttributeValueDescriptionsUri
     */
    protected $listAttributeValueDescriptionsUri = 'listAttributeValueDescriptions';

    /**
     * @var ListAttributeDataProvider $listAttributeDataProvider
     */
    protected $listAttributeDataProvider;

    /**
     * ListAttributeValueDescriptionMigration constructor.
     * @param ListAttributeDataProvider $listAttributeDataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     */
    public function __construct(
        ListAttributeDataProvider $listAttributeDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->listAttributeDataProvider = $listAttributeDataProvider;
        parent::__construct($listAttributeDataProvider, $apiCall, $container);
    }

    /**
     * @param $listAttribute
     */
    public function process($listAttribute)
    {
        foreach ($listAttribute['values'] as $attributeValue => $value) {

            $listAttributeValueOuterId = $this
                ->listAttributeDataProvider
                ->getListAttributeValueOuterId($attributeValue);

            $listAttributeValueDescriptionData['name'] = (string)$attributeValue;
            $listAttributeValueDescriptionData['listAttributeValue']['id'] = $listAttributeValueOuterId;
            $listAttributeValueDescriptionData['language']['id'] = $this->hungarianLanguageId;

            $this->addToBatchArray($this->listAttributeValueDescriptionsUri, '', $listAttributeValueDescriptionData);
        }
    }
}