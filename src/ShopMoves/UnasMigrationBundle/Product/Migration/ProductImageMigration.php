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

    protected $productImageUri = '/productImages/';

    protected $productFileUri = '/files';

    public function __construct(ProductDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {

        $imageDownloader = $this->container->get('image_downloader');

        if ($this->isProductDeleted($product) || !isset($product->Images)) {
            return;
        }

        $image = $product->Images->Image;
        if (is_array($image)) {
            foreach ($image as $img) {
                $outerId = $this->getOuterId(basename($img->Url->Medium));
                $fileContent = $imageDownloader->getFileEncodedContent($img->Url->Medium);
                $fileData['filePath'] = 'product/' . basename($img->Url->Medium);
                $fileData['type'] = 'image';
                $fileData['attachment'] = $fileContent;

                $data['id'] = $outerId;
                $data['imagePath'] = basename($img->Url->Medium);
                $data['product']['id'] = $this->getProductOuterId($product);
                $this->addToBatchArray($this->productFileUri, '', $fileData);
                $this->addToBatchArray($this->productImageUri, $outerId, $data);
            }
        } else {
            $outerId = $this->getOuterId(basename($image->Url->Medium));
            $fileContent = $imageDownloader->getFileEncodedContent($image->Url->Medium);
            $fileData['filePath'] = 'product/' . basename($image->Url->Medium);
            $fileData['type'] = 'image';
            $fileData['attachment'] = $fileContent;

            $data['id'] = $outerId;
            $data['imagePath'] = basename($image->Url->Medium);
            $data['product']['id'] = $this->getProductOuterId($product);

            $this->addToBatchArray($this->productFileUri, '', $fileData);
            $this->addToBatchArray($this->productImageUri, $outerId, $data);
        }
    }

    public function getOuterId($imageName)
    {
        return base64_encode('product-ProductImageName=' . $imageName);
    }
}