<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.05.
 * Time: 13:35
 */

namespace ShopMoves\UnasMigrationBundle\Category\Provider;


use ShopMoves\UnasMigrationBundle\Provider\DataProvider;

class CategoryDataProvider extends DataProvider
{
    protected $fileName = 'kiscipoproduct';

    protected $extension = 'json';

    public function getData()
    {
        $fileUrl = $this->getFileUrl($this->fileName, $this->extension);
        $content = file_get_contents($fileUrl);

        $products = json_decode($content);

        return $products->Products->Product;
    }
}