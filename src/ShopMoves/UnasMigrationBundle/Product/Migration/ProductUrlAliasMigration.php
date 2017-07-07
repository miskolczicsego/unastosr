<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.06.
 * Time: 17:00
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductUrlAliasMigration extends BatchMigration
{

    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product)) {
            return;
        }

        $alias = $this->getUrlAliasToProduct($product);
        $outerId = $this->getOuterId($alias);
        $data['id'] = $outerId;
        $data['urlAlias'] = $alias;
        $data['type'] = 'PRODUCT';
        $data['urlAliasEntity']['id'] = $this->getProductOuterId($product);

        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => 'http://demo.api.aurora.miskolczicsego/urlAliases/' . $outerId,
            'data' => $data
        ];
    }

    public function getOuterId($data)
    {
        return base64_encode($data);
    }

    public function getUrlAliasToProduct($product)
    {
        $urlParts = explode('/', $product->Url);
        return $urlParts[count($urlParts) - 1];

    }
}