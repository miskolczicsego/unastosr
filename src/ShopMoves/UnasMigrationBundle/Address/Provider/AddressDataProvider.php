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
    protected $fileName = 'kiscipocustomer';

    protected $extension = 'json';

    public function getData()
    {
        $json = $this->getFileContentAsJson();
        $addresses = [];
        foreach ($json->Customers->Customer as $customer){
            $addresses['addresses'][] = [
                'customerId' => $customer->Id,
                'address' => $customer->Addresses
            ];
        }
        return $addresses;

    }

    public function getShippingOuterId($address)
    {
        return base64_encode(
            $address['address']->Shipping->Name .
            $address['address']->Shipping->ZIP .
            $address['address']->Shipping->City .
            $address['address']->Shipping->Street .
            $address['address']->Shipping->Country
        );
    }

    public function getInvoiceOuterId($address)
    {
        return base64_encode(
            $address['address']->Invoice->Name .
            $address['address']->Invoice->ZIP .
            $address['address']->Invoice->City .
            $address['address']->Invoice->Street .
            $address['address']->Invoice->Country
        );
    }
}