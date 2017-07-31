<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 15:42
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Attributes\Provider\ListAttributeDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductClassDataProvider;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductMigration extends BatchMigration
{

    protected $productUri = 'products';


    protected $mainImageToProduct;

    protected $taxHelper;

    /**
     * @var ProductDataProvider $productDataProvider
     */
    protected $productDataProvider;

    protected $productClassDataProvider;


    protected $skus = [];


    /**
     * ProductMigration constructor.
     * @param ProductDataProvider $productDataProvider
     * @param ProductClassDataProvider $productClassDataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     **/
    public function __construct(
        ProductDataProvider $productDataProvider,
        ProductClassDataProvider $productClassDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->productClassDataProvider = $productClassDataProvider;
        $this->taxHelper = $container->get('tax_helper');
        parent::__construct($productDataProvider, $apiCall, $container);
    }

    /**
     * @param $product
     */

    public function process($product)
    {

        if ($this->productDataProvider->isProductDeleted($product)) {
            return;
        }

        $this->skus[$product->Sku] = true;


        $unasStatus = $product->Statuses->Status->Value;

        $srStatus = $unasStatus == '0' ? '0' : '1';
        $productOuterId = $this->productDataProvider->getProductOuterId($product->Sku);
        $productData['id'] = $productOuterId;
        $productData['sku'] = $product->Sku;
        $productData['status'] = $srStatus;
        if ($unasStatus == '3') {
            $productData['orderable'] = 0;
        }
        $productData['price'] = count($product->Prices->Price) == 1 ?
            $product->Prices->Price->Net :
            $this->productDataProvider->getProductPrice($product->Prices->Price);

        $productData['stock1'] = $this->productDataProvider->getProductQuantity($product);

        $productData['taxClass'] = [
            'id' => $this->taxHelper->getTaxId($product->Prices->Vat)
        ];
        $productData['mainPicture'] = $this->productDataProvider->getMainPictureToProduct($product);

        $productData['productClass']['id'] = $this->productClassDataProvider->getProductClassIdToProduct($product);

        $this->addToBatchArray($this->productUri, $productOuterId, $productData);
    }
}