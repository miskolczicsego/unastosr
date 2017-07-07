<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 17:26
 */

namespace ShopMoves\UnasMigrationBundle\Address\Provider;


use ShopMoves\UnasMigrationBundle\Provider\DataProvider;

class AddressDataProvider extends DataProvider
{
    protected $fileName = 'customer';

    protected $extension = 'json';

    public function _getData()
    {
        $fileUrl = $this->getFileUrl($this->fileName, $this->extension);
        $content = file_get_contents($fileUrl);
        $addresses = [];
        $customerObject = json_decode($content);
        foreach ($customerObject->Customers->Customer as $customer){
            $addresses['addresses'][] = [
                'customerId' => $customer->Id,
                'address' => $customer->Addresses
            ];
        }
        return $addresses;

    }
}