<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.21.
 * Time: 15:37
 */

namespace ShopMoves\UnasMigrationBundle\Helper;


class LanguageHelper
{
    public function getLanguages()
    {
        return [
            'HU' => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
        ];
    }

    public function getLanguageByKey($key)
    {
        return $this->getLanguages()[$key];
    }
}