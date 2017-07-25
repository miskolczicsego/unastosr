<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.10.
 * Time: 13:23
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use Behat\Transliterator\Transliterator;
use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Migration\ListAttributeMigration;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductClassMigration extends BatchMigration
{

    protected $productClassUri = 'productClasses';


    protected $productClasses = [];



    public function __construct(
        ProductDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {

        parent::__construct($dataProvider, $apiCall, $container);
    }
    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Params) || empty($product->Params)) {
            return;
        }

        $classes = $product->Params->Param;

        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->buildClassBatch($class);
            }
        } else {
            $this->buildClassBatch($classes);
        }
    }

    public function getProductClassOuterId($class)
    {
        return base64_encode('product-Product-Class=' . $class->Name . $this->timeStamp);
    }

    public function buildClassBatch($class)
    {
        if(!array_key_exists($class->Name, $this->productClasses)) {
            $outerId = $this->getProductClassOuterId($class);
            $this->productClasses[$class->Name] = [
                'id' => $outerId
            ];
            $data['id'] = $outerId;
            $data['name'] = 'Változó ' . $class->Name . ' szerint';
            $data['firstVariantSelectType'] = 'LIST';
            $data['firstVariantParameter']['id'] = base64_encode(Transliterator::transliterate($class->Name, '_') . $this->timeStamp);
            $this->addToBatchArray($this->productClassUri, $outerId, $data);
        }
    }

    public function getProductClasses()
    {
        return $this->productClasses;
    }
}