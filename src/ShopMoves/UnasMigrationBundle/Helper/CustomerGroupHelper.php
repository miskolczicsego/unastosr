<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.21.
 * Time: 14:19
 */

namespace ShopMoves\UnasMigrationBundle\Helper;


class CustomerGroupHelper
{
    public function getDefaultSRCustomerGroup()
    {
        return base64_encode('customerGroup-customer_group_id=8');
    }
}