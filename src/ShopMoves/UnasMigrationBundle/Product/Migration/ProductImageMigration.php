<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.10.
 * Time: 10:49
 */

namespace ShopMoves\UnasMigrationBundle\Product\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductImageMigration extends BatchMigration
{

    protected $productImageUri = 'productImages';

    protected $productDataProvider;

    protected $imageDownloader;

    public function __construct(
        ProductDataProvider $productDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->productDataProvider = $productDataProvider;
        parent::__construct($productDataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->productDataProvider->isProductDeleted($product) ||
            !isset($product->Images)
        ) {
            return;
        }

        $images = $product->Images->Image;

        if (is_array($images)) {
            foreach ($images as $image) {
                $this->buildImageAndFileBatch($image, $product);
            }
        } else {
           $this->buildImageAndFileBatch($images, $product);
        }
    }

    /**
     * @param $image
     * @param $product
     */
    public function buildImageAndFileBatch($image, $product)
    {
        $path = 'product/' . basename($image->Url->Medium);

        if (isset($image->Type) && $image->Type != 'base') {
            $productImageData['imagePath'] = $path;
            $productImageData['product']['id'] = $this->productDataProvider->getProductOuterId($product->Sku);
            $this->addToBatchArray($this->productImageUri, '', $productImageData);
        }
    }
}