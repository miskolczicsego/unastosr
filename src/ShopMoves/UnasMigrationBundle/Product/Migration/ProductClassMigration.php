<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.10.
 * Time: 13:23
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductClassMigration extends BatchMigration
{

    protected $productClassUri = '/productClasses/';

    protected $productClasses = [];


    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }
    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Params) || empty($product->Params)) {
            return;
        }

        $classes = $product->Params->Param;

        dump($classes);die;

        if (is_array($classes)) {
            foreach ($classes as $class) {

            }
        } else {
            if($classes->Name === 'Típus' && !array_key_exists($classes->Value, $this->productClasses)) {
                $this->productClasses[$classes->Value] = true;
                $outerId = $this->getOuterId($classes);
                $data['id'] = $outerId;
                $data['name'] = $classes->Value;
                $this->addToBatchArray($this->productClassUri, $outerId, $data);
            }
        }

    }

    public function getOuterId($data)
    {
        return base64_encode('product_class-Product=' . $data->Value);
    }

    public function buildClassBatch()
    {
        if(isset($class) && $class->Name === 'Típus' && !array_key_exists($class->Value, $this->productClasses)) {
            $this->productClasses[$class->Value] = true;
            $outerId = $this->getOuterId($class);
            $data['id'] = $outerId;
            $data['name'] = $class->Value;
            $this->addToBatchArray($this->productClassUri, $outerId, $data);
        }
    }
}