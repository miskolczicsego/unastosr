<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 16:27
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Iterator\Provider\IDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductDescriptionMigration extends BatchMigration
{

    protected $productDescriptionUri = '/productDescriptions/';

    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product)) {
            return;
        }
        $outerId = $this->getOuterId($product);

        $data['id'] = $this->getOuterId($product);
        $data['name'] = $product->Name;
        $data['shortDescription'] = isset($product->Description->Short) ? $product->Description->Short : '';
        $data['description'] = isset($product->Description->Long) ? $product->Description->Long : '';
        $data['packagingUnit'] = $product->Unit;
        if(isset($product->Meta)) {
            $data['metaKeywords'] = isset($product->Meta->Keywords) ? $product->Meta->Keywords : '';
            $data['metaDescription'] = isset($product->Meta->Description) ? $product->Meta->Description : '';
        }


        $data['product'] = [
            "id" => $this->getProductOuterId($product)
        ];
        //fixen a magyar nyelv resource id-ja egyenlőre
        $data['language'] = [
            "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
        ];

        $this->addToBatchArray($this->productDescriptionUri, $outerId, $data);
    }

    public function getOuterId($product)
    {
        return base64_encode('product_description-Product='.$product->Id);
    }
}