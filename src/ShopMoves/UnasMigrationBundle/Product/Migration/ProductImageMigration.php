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
use ShopMoves\UnasMigrationBundle\Utils\ImageDownloader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductImageMigration extends BatchMigration
{

    protected $productImageUri = '/productImages/';

    protected $productFileUri = '/files';

    protected $imageDownloader;

    public function __construct(ImageDownloader $imageDownloader, ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        $this->imageDownloader = $imageDownloader;
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {
        if ($this->isProductDeleted($product) || !isset($product->Images)) {
            return;
        }

        $image = $product->Images->Image;
        if (is_array($image)) {
            foreach ($image as $img) {
                $this->buildImageAndFileBatch($img, $product);
            }
        } else {
           $this->buildImageAndFileBatch($image, $product);
        }

        unset($product);
        unset($image);

    }

    public function getOuterId($imageName)
    {
        return base64_encode('product-ProductImageName=' . $imageName);
    }

    /**
     * @param $image
     * @param ImageDownloader $imageDownloader
     * @param $product
     */
    public function buildImageAndFileBatch($image, $product)
    {
        $outerId = $this->getOuterId(basename($image->Url->Medium));
        $path = 'product/' . basename($image->Url->Medium);
        $fileContent = $this->imageDownloader->getFileEncodedContent($image->Url->Medium);
        if($fileContent === false) {
            unset($product);
            unset($image);
            return;
        }
        $fileData['filePath'] = $path;
        $fileData['type'] = 'image';
        $fileData['attachment'] = $fileContent;

        if (isset($image->Type) && $image->Type != 'base') {
            $data['id'] = $outerId;
            $data['imagePath'] = $path;
            $data['product']['id'] = $this->getProductOuterId($product);
            $this->addToBatchArray($this->productImageUri, $outerId, $data);
        }
        $this->addToBatchArray($this->productFileUri, '', $fileData);

        unset($product);
        unset($image);
    }
}