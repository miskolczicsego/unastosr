<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.19.
 * Time: 13:40
 */

namespace ShopMoves\UnasMigrationBundle\Attributes\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListAttributeMigration extends BatchMigration
{
    /**
     * @var string $listAttributesUri
     */
    protected $listAttributesUri = 'listAttributes';

    /**
     * @var array $listAttributeIds
     */
    protected $listAttributeIds = [];

    /**
     * @var ListAttributeDataProvider $listAttributeDataProvider
     */
    protected $listAttributeDataProvider;


    /**
     * ListAttributeMigration constructor.
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
        $listAttributeOuterId = $this
            ->listAttributeDataProvider
            ->getListAttributeOuterId($listAttribute['slug']);

        $this->collectListAttributeIds($listAttributeOuterId);

        $listAttributeData['id'] = $listAttributeOuterId;
        $listAttributeData['type'] = $listAttribute['type'];
        $listAttributeData['name'] = $listAttribute['slug'];
        $listAttributeData['priority'] = 'NORMAL';
        $listAttributeData['presentation'] = 'TEXT';

        $this->addToBatchArray($this->listAttributesUri, $listAttributeOuterId, $listAttributeData);
    }

    public function collectListAttributeIds($listAttributeOuterId)
    {
        $this->listAttributeIds[] = $listAttributeOuterId;
    }

    public function getListAttributeIds()
    {
        return $this->listAttributeIds;
    }
}