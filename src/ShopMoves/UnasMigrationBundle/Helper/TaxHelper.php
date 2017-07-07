<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.06.
 * Time: 14:32
 */

namespace ShopMoves\UnasMigrationBundle\Helper;


class TaxHelper
{
    public function getTaxId($tax)
    {
        $taxes = [
            '27%' => base64_encode('taxClass-tax_class_id=10')
        ];

        return $taxes[$tax];
    }
}