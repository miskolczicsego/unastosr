<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 14:11
 */

namespace ShopMoves\UnasMigrationBundle\Address\Migration;


use ShopMoves\UnasMigrationBundle\Address\Provider\AddressDataProvider;
use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddressMigration extends BatchMigration
{

    public function __construct(AddressDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($addresses)
    {
        foreach ($addresses as $address) {

            $nameHelper = $this->container->get('name_helper');

            $shippingName = $nameHelper->separate($address['address']->Shipping->Name);
            $invoiceName = $nameHelper->separate($address['address']->Invoice->Name);
            $shippingOuterId = $this->getShippingOuterId($address);
            $invoiceOuterId = $this->getInvoiceOuterId($address);

            //TODO: TaxNumbert bevinni
            if ($this->isAddressesEqual($address)) {
                $data['id'] = $shippingOuterId;
                $data['firstname'] = $shippingName['firstname'];
                $data['lastname'] = $shippingName['lastname'];
                $data['address1'] = $address['address']->Shipping->Street;
                $data['postcode'] = $address['address']->Shipping->ZIP;
                $data['city'] = $address['address']->Shipping->City;
                $data['country']['id'] = 'Y291bnRyeS1jb3VudHJ5X2lkPTE';
                $data['customer']['id'] = base64_encode($address['customerId']);
                $this->batchData['requests'][] =
                    [
                        'method' => 'POST',
                        'uri' => 'http://demo.api.aurora.miskolczicsego/addresses/' . $shippingOuterId,
                        'data' => $data
                    ];
            } else {
                $dataShipping['id'] = $shippingOuterId;
                $dataShipping['firstname'] = $shippingName['firstname'];
                $dataShipping['lastname'] = $shippingName['lastname'];
                $dataShipping['address1'] = $address['address']->Shipping->Street;
                $dataShipping['postcode'] = $address['address']->Shipping->ZIP;
                $dataShipping['city'] = $address['address']->Shipping->City;
                $dataShipping['country']['id'] = 'Y291bnRyeS1jb3VudHJ5X2lkPTE';
                $dataShipping['customer']['id'] = base64_encode($address['customerId']);

                $this->batchData['requests'][] =
                    [
                        'method' => 'POST',
                        'uri' => 'http://demo.api.aurora.miskolczicsego/addresses/' . $shippingOuterId,
                        'data' => $dataShipping
                    ];

                $dataInvoice['id'] = $invoiceOuterId;
                $dataInvoice['firstname'] = $invoiceName['firstname'];
                $dataInvoice['lastname'] = $invoiceName['lastname'];
                $dataInvoice['address1'] = $address['address']->Invoice->Street;
                $dataInvoice['postcode'] = $address['address']->Invoice->ZIP;
                $dataInvoice['city'] = $address['address']->Invoice->City;
                $dataInvoice['country']['id'] = 'Y291bnRyeS1jb3VudHJ5X2lkPTE';
                $dataInvoice['customer']['id'] = base64_encode($address['customerId']);
                $this->batchData['requests'][] =
                    [
                        'method' => 'POST',
                        'uri' => 'demo.api.aurora.miskolczicsego/addresses/' . $invoiceOuterId,
                        'data' => $dataInvoice
                    ];
            }
        }
    }

    public function isAddressesEqual($address)
    {
        if(
            $address['address']->Invoice->Name != $address['address']->Shipping->Name ||
            $address['address']->Invoice->ZIP != $address['address']->Shipping->ZIP ||
            $address['address']->Invoice->City != $address['address']->Shipping->City ||
            $address['address']->Invoice->Street != $address['address']->Shipping->Street ||
            $address['address']->Invoice->StreetName != $address['address']->Shipping->StreetName ||
            $address['address']->Invoice->County != $address['address']->Shipping->County ||
            $address['address']->Invoice->Country != $address['address']->Shipping->Country ||
            $address['address']->Invoice->CountryCode != $address['address']->Shipping->CountryCode
        ) {
            return false;
        }

        return true;
    }

    public function getShippingOuterId($address)
    {
        return base64_encode(
            $address['address']->Shipping->Name .
            $address['address']->Shipping->ZIP .
            $address['address']->Shipping->City .
            $address['address']->Shipping->Street
        );
    }

    public function getInvoiceOuterId($address)
    {
        return base64_encode(
            $address['address']->Invoice->Name .
            $address['address']->Invoice->ZIP .
            $address['address']->Invoice->City .
            $address['address']->Invoice->Street
        );
    }

    public function getOuterId($data)
    {
        // TODO: Implement getOuterId() method.
    }
}