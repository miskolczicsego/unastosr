<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.25.
 * Time: 12:57
 */

namespace ShopMoves\UnasMigrationBundle\Attributes\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListAttributeValueMigration extends BatchMigration
{
    /**
     * @var string $listAttributeValuesUri
     */
    protected $listAttributeValuesUri = 'listAttributeValues';

    /**
     * @var ListAttributeDataProvider $listAttributeDataProvider
     */
    protected $listAttributeDataProvider;

    /**
     * ListAttributeValueMigration constructor.
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
     * @param array $listAttribute
     */
    public function process($listAttribute)
    {
        $listAttributeOuterId = $this
            ->listAttributeDataProvider
            ->getListAttributeOuterId($listAttribute['slug']);

        foreach ($listAttribute['values'] as $attributeValue => $value) {

            $listAttributeValueOuterId = $this
                ->listAttributeDataProvider
                ->getListAttributeValueOuterId($attributeValue);

            $listAttributeValueData['id'] = $listAttributeValueOuterId;
            $listAttributeValueData['listAttribute']['id'] = $listAttributeOuterId;

            $this->addToBatchArray($this->listAttributeValuesUri, $listAttributeValueOuterId, $listAttributeValueData);
        }

    }
}