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
    protected $fileName = 'kiscipocustomer';

    protected $extension = 'json';

    public function getData()
    {
        $json = $this->getFileContentAsJson();

        return $json->Customers->Customer;
    }

    /**
     * @param $customerId
     * @return string
     */
    public function getCustomerOuterId($customerId)
    {
        return base64_encode('customer-customerId=' . $customerId);
    }

    /**
     * @param $customerGroupId
     * @return string
     */
    public function getCustomerGroupOuterId($customerGroupId)
    {
        return base64_encode('customer-customerGroup=' . $customerGroupId);
    }

    public function getCustomerPhoneNumber($customer)
    {
        if (is_string($customer->Contact->Mobile)) {
            return $customer->Contact->Mobile;
        }
        return $customer->Contact->Phone;
    }

    /**
     * @param $email
     * @return bool|false|string
     */
    public function generatePasswordFromEmail($email)
    {
        return password_hash($email, PASSWORD_DEFAULT);
    }
}