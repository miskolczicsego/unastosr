<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 16:50
 */

namespace ShopMoves\UnasMigrationBundle\Customer\Provider;


use ShopMoves\UnasMigrationBundle\Provider\DataProvider;

class CustomerDataProvider extends DataProvider
{
    protected $fileName = 'customer';

    protected $extension = 'json';

    public function getData()
    {
        $fileUrl = $this->getFileUrl($this->fileName, $this->extension);
        $content = file_get_contents($fileUrl);

        $customerObject = json_decode($content);
        return $customerObject->Customers->Customer;
    }
}