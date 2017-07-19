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
    protected $listAttributesUri = '/listAttributes/';
    protected $attributeDescriptionsUri = '/attributeDescriptions';
    protected $listAttributeValuesUri = '/listAttributeValues/';
    protected $listAttributeValueDescriptionsUri = '/listAttributeValueDescriptions';
    protected $productClassAttributeRelationUri = '/productClassAttributeRelations';

    public function __construct(ListAttributeDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($listAttribute)
    {
        $listAttributeOuterId = $this->getOuterId($listAttribute['slug']);

        $listAttributeData['id'] = $listAttributeOuterId;
        $listAttributeData['type'] = $listAttribute['type'];
        $listAttributeData['name'] = $listAttribute['slug'];
        $listAttributeData['priority'] = 'NORMAL';
        $listAttributeData['presentation'] = 'TEXT';

        $attributeDescriptionData['name'] = $listAttribute['name'];
        $attributeDescriptionData['language']['id'] = 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ==';
        $attributeDescriptionData['attribute']['id'] = $listAttributeOuterId;

        $classids = $this->dataProvider->getProductClassIds();

        $this->addToBatchArray($this->listAttributesUri, $listAttributeOuterId, $listAttributeData);
        $this->addToBatchArray($this->attributeDescriptionsUri, '', $attributeDescriptionData);

        foreach ($classids as $name => $id) {
            $productClassToAttribute['attribute']['id'] = $listAttributeOuterId;
            $productClassToAttribute['productClass']['id'] = $id;

            $this->addToBatchArray($this->productClassAttributeRelationUri, '', $productClassToAttribute);
        }

        foreach ($listAttribute['values'] as $attributeValue => $value) {
//            dump($key);die;
            $listAttributeValueOuterId = $this->getListAttributeValueOuterId($attributeValue);

            $listAttributeValueData['id'] = $listAttributeValueOuterId;
            $listAttributeValueData['listAttribute']['id'] = $listAttributeOuterId;

            $listAttributeValueDescriptionData['name'] = $attributeValue;
            $listAttributeValueDescriptionData['listAttributeValue']['id'] = $listAttributeValueOuterId;
            $listAttributeValueDescriptionData['language']['id'] = 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ==';


            $this->addToBatchArray($this->listAttributeValuesUri, $listAttributeValueOuterId, $listAttributeValueData);
            $this->addToBatchArray($this->listAttributeValueDescriptionsUri, '', $listAttributeValueDescriptionData);
        }
    }

    public function getOuterId($data)
    {
        return base64_encode('product_listAttribute=' . $data);
    }

    public function getListAttributeValueOuterId($data)
    {
        return base64_encode('product_listAttributeValue=' . $data);
    }
}