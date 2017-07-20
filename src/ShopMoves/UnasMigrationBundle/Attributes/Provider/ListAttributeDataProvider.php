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

class ListAttributeDataProvider extends DataProvider
{

    protected $listAttributeDatas =  [];

    /**
     * @var Transliterator $slugifier
     */
    protected $slugifier;

    protected $fileName = 'kiscipoproduct';

    protected $extension = 'json';

    protected $productClassIds = [];

    protected $attributeValueToProduct = [];

    function __construct($container)
    {
        parent::__construct($container);
    }

    public function _getData()
    {
        $this->slugifier = $this->container->get('slugifier');
        $fileUrl = $this->getFileUrl($this->fileName, $this->extension);
        $content = file_get_contents($fileUrl);

        $json = json_decode($content);

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
            $this->listAttributeDatas[$param->Name] = [
                'type' => 'LIST',
                'slug' => $this->slugifier->transliterate($param->Name, '_'),
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
            $this->productClassIds[$class->Name] = base64_encode('product-Product-Class=' . $class->Id);
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
            'listAttributeId' => $this->getListAttributeValueOuterId($data->Value)
        ];

    }

    public function getListAttributeValueOuterId($data)
    {
        return base64_encode('product_listAttributeValue=' . $data);
    }

    public function getProductOuterId($product)
    {
        return base64_encode('product_id-Product=' . $product->Sku);
    }
}