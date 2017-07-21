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
use ShopMoves\UnasMigrationBundle\Helper\LanguageHelper;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListAttributeMigration extends BatchMigration
{
    protected $listAttributesUri = 'listAttributes';
    protected $attributeDescriptionsUri = 'attributeDescriptions';
    protected $listAttributeValuesUri = 'listAttributeValues';
    protected $listAttributeValueDescriptionsUri = 'listAttributeValueDescriptions';
    protected $attributeHrefs = [];

    /**
     * @var LanguageHelper $languageHelper
     */
    protected $languageHelper;


    protected $hungarianLanguageId;


    public function __construct(
        ListAttributeDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->languageHelper = $container->get('language_helper');
        $this->hungarianLanguageId = $this->languageHelper->getLanguageByKey('HU');
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($listAttribute)
    {
        $listAttributeOuterId = $this->getOuterId($listAttribute['slug']);

        $listAttributeData['id'] = $listAttributeOuterId;

        $this->collectListAttributeIds($listAttributeOuterId);

        $listAttributeData['type'] = $listAttribute['type'];
        $listAttributeData['name'] = $listAttribute['slug'];
        $listAttributeData['priority'] = 'NORMAL';
        $listAttributeData['presentation'] = 'TEXT';

        $attributeDescriptionOuterId = $this->getAttributeDescriptionOuterId($listAttribute['name']);
        $attributeDescriptionData['id'] = $attributeDescriptionOuterId;
        $attributeDescriptionData['name'] = $listAttribute['name'];
        $attributeDescriptionData['language']['id'] = $this->hungarianLanguageId;
        $attributeDescriptionData['attribute']['id'] = $listAttributeOuterId;

        $this->addToBatchArray($this->listAttributesUri, $listAttributeOuterId, $listAttributeData);
        $this->addToBatchArray($this->attributeDescriptionsUri, $attributeDescriptionOuterId, $attributeDescriptionData);

        foreach ($listAttribute['values'] as $attributeValue => $value) {
            $listAttributeValueOuterId = $this->getListAttributeValueOuterId($attributeValue);

            $listAttributeValueData['id'] = $listAttributeValueOuterId;
            $listAttributeValueData['listAttribute']['id'] = $listAttributeOuterId;

            $listAttributeValueDescriptionDataOuterId = $this->getListAttributeValueDescriptionData($attributeValue);
            $listAttributeValueDescriptionData['id'] = $listAttributeValueDescriptionDataOuterId;
            $listAttributeValueDescriptionData['name'] = $attributeValue;
            $listAttributeValueDescriptionData['listAttributeValue']['id'] = $listAttributeValueOuterId;
            $listAttributeValueDescriptionData['language']['id'] = $this->hungarianLanguageId;


            $this->addToBatchArray($this->listAttributeValuesUri, $listAttributeValueOuterId, $listAttributeValueData);
            $this->addToBatchArray($this->listAttributeValueDescriptionsUri, $listAttributeValueDescriptionDataOuterId, $listAttributeValueDescriptionData);
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
    public function getAttributeDescriptionOuterId($data)
    {
        return base64_encode('product_attributeDescription=' . $data);
    }
    public function getListAttributeValueDescriptionData($data)
    {
        return base64_encode('product_ListAttributeValueDescription=' . $data);
    }

    public function collectListAttributeIds($listAttributeOuterId)
    {
        $this->attributeHrefs[] = $listAttributeOuterId;
    }

    public function getAttributeIds()
    {
        return $this->attributeHrefs;
    }
}