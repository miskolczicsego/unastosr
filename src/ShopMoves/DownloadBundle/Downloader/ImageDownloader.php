<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.13.
 * Time: 14:07
 */

namespace ShopMoves\DownloadBundle\Downloader;


use ShopMoves\UnasMigrationBundle\Product\Provider\ProductDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageDownloader
{
    /**
     * @var ProductDataProvider
     */
    protected $productDataProvider;

    protected $products;

    protected $imageUrls;

    protected $container;

    function __construct(ProductDataProvider $productDataProvider, ContainerInterface $container)
    {
        $this->container = $container;
        $this->products = $productDataProvider->_getData();
    }


    public function download()
    {

        foreach ($this->products as $product) {
            if ($product->State !== 'live' ||
                !isset($product->Images)
            ) {
                continue;
            }

            if (is_array($product->Images->Image)) {
                foreach ($product->Images->Image as $image) {
                    $url = $image->Url->Medium;
                    $url = str_replace("%3A", ":", implode("/", array_map("rawurlencode", explode("/", $url))));
                    $this->downloadImage($url, $this->container->getParameter('kernel.root_dir') . '/images/' .  basename($url));
                }
                } else {
                $url = $product->Images->Image->Url->Medium;
                $url = str_replace("%3A", ":", implode("/", array_map("rawurlencode", explode("/", $url))));
                $this->downloadImage($url, $this->container->getParameter('kernel.root_dir') . '/images/' .  basename($url));
            }

        }
    }
    public function downloadImage($url, $path)
    {
        if (!file_exists($path)) {
            $content = @file_get_contents($url);
            if (!empty($content) && @getimagesize($url)) {
                file_put_contents($path, $content);
                return true;
            }
            return false;
        }
        return true;
    }
}
