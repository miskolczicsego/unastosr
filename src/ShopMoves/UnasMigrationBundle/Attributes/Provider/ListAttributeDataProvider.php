<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.19.
 * Time: 13:08
 */

namespace ShopMoves\UnasMigrationBundle\Attributes\Provider;


use Behat\Transliterator\Transliterator;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListAttributeDataProvider extends DataProvider
{

    protected $listAttributeDatas =  [];

    /**
     * @var Transliterator $slugifier
     */
    protected $slugifier;

    protected $fileName = '1501662054503product';

    protected $extension = 'json';

    protected $productClassIds = [];

    protected $attributeValueToProduct = [];

    protected $timeStamp;

    /**
     * ListAttributeDataProvider constructor.
     * @param ContainerInterface $container
     */
    function __construct($container)
    {
        $this->timeStamp = $container->get('timestamp_provider')->getTimestamp();
        parent::__construct($container);
    }

    public function getData()
    {
        $this->slugifier = $this->container->get('slugifier');

        $json = $this->getFileContentAsJson();

        $products = $json->Products->Product;
        foreach ($products as $product) {
            if(isset($product->Params)) {
                $params = $product->Params->Param;
                if(is_array($params)) {
                    foreach ($params as $param) {
                        $this->setProductClassId($param);
                        $this->gatherListAttributeDatas($param);
                        $this->gatherAttributeValuesToProduct($product, $param);
                    }
                } else {
                    $this->setProductClassId($params);
                    $this->gatherListAttributeDatas($params);
                    $this->gatherAttributeValuesToProduct($product, $params);
                }
            }

            if (isset($product->Datas)) {
            $datas = $product->Datas->Data;
                if(is_array($datas)) {
                    foreach ($datas as $data) {
                        $this->gatherListAttributeDatas($data);
                        $this->gatherAttributeValuesToProduct($product, $data);
                    }
                } else {
                    $this->gatherListAttributeDatas($datas);
                    $this->gatherAttributeValuesToProduct($product, $datas);
                }
            }

        }
        return $this->listAttributeDatas;

    }

    public function getListAttributeDatas()
    {
        return $this->listAttributeDatas;
    }

    public function gatherListAttributeDatas($param)
    {
        if(!array_key_exists($param->Name, $this->listAttributeDatas)) {
            $slug = $this->getSlugifiedNameOfAttribute($param->Name);
            $this->listAttributeDatas[$param->Name] = [
                'id' => $param->Id,
                'outerId' => $this->getListAttributeOuterId($slug),
                'type' => 'LIST',
                'slug' => $slug,
                'name' => $param->Name,
                'values' => [$param->Value => 1]
            ];
        } else  {
            if (!array_key_exists($param->Value, $this->listAttributeDatas[$param->Name]['values'])) {
                $this->listAttributeDatas[$param->Name]['values'][$param->Value] = 1;
            }
        }
    }

    public function setProductClassId($class)
    {
        if (!array_key_exists($class->Id, $this->productClassIds)) {
            $this->productClassIds[$class->Name] = base64_encode('product-Product-Class=' . $class->Name);
        }
    }

    public function getAttributeValueToProductBySku($sku) {
        return isset($this->attributeValueToProduct[$sku]) ? $this->attributeValueToProduct[$sku] : '';
    }

    public function getProductClassIds()
    {
        return $this->productClassIds;
    }

    public function gatherAttributeValuesToProduct($product, $data)
    {
        $this->attributeValueToProduct[$product->Sku][] = [
            'listAttributeValueId' => $this->getListAttributeValueOuterId($data->Value)
        ];

    }

    public function getSlugifiedNameOfAttribute($name)
    {
        return Transliterator::transliterate($name, '_');
    }

    /**
     * @param $attributeValue
     * @return string
     */
    public function getListAttributeValueOuterId($attributeValue)
    {
        return base64_encode('product_listAttributeValue=' . $attributeValue);
    }

    /**
     * @param $slug
     * @return string
     */
    public function getListAttributeOuterId($slug)
    {
        return base64_encode('product_listAttribute=' . $slug);
    }
}