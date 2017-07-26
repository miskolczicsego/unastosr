<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.26.
 * Time: 10:10
 */

namespace ShopMoves\UnasMigrationBundle\Product\Provider;


use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductClassDataProvider extends DataProvider
{

    protected $fileName = 'kiscipoproduct';

    protected $extension = 'json';

    protected $productClassMigration;

    protected $productDataProvider;

    protected $productClasses = [];
    function __construct(
        ProductDataProvider $productDataProvider,
        ContainerInterface $container
    ) {
        $this->productDataProvider = $productDataProvider;
        parent::__construct($container);
    }

    public function getData()
    {
        $products = $this->getFileContentAsJson()->Products->Product;
        $this->productClasses['Nincs terméktípus'] = [
            'name' => 'Nincs terméktípus'
        ];
        foreach ($products as $product) {

            if ($this->productDataProvider->isProductDeleted($product) ||
                !isset($product->Params) ||
                empty($product->Params)
            ) {
                continue;
            }

            $classes = $product->Params->Param;

            if (is_array($classes)) {
                foreach ($classes as $class) {
                   $this->gatherProductClasses($class);
                }
            } else {
                $this->gatherProductClasses($classes);
            }
        }
        return $this->productClasses;
    }

    public function gatherProductClasses($class)
    {
        if (!array_key_exists($class->Name, $this->productClasses)) {
            $this->productClasses[$class->Name] = [
                'name' => $class->Name
            ];
        }
    }

    public function getProductClasses()
    {
        return $this->productClasses;
    }

    public function getProductClassOuterId($className)
    {
        return base64_encode('product-productClass=' . $className . $this->timeStamp);
    }



    public function getProductClassIdToProduct($product)
    {
        $defaultClass = 'Nincs terméktípus';

        if (isset($product->Params) && is_array($product->Params->Param)) {
            $class = array_pop($product->Params->Param);
            return $this->getProductClassOuterId($class->Name);
        } elseif (isset($product->Params) && !is_array($product->Params->Param)) {
            return $this->getProductClassOuterId($product->Params->Param->Name);
        } else {
            return $this->getProductClassOuterId($defaultClass);
        }
    }
}