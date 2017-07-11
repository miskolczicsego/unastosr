<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.05.
 * Time: 14:16
 */

namespace ShopMoves\UnasMigrationBundle\Helper;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Api\Response;

class CountryHelper
{
    protected $apicall;

    protected $srCountryDatas;
    function __construct(ApiCall $apicall)
    {
        $this->apicall = $apicall;

    }

    public function getSRCountries($page = 0, $limit = 200)
    {
        /** @var Response $response */
        $response = $this->apicall->execute('GET', '/countries?page=' . $page . '&limit=' . $limit, '');

        if($response->getCode() !== 400) {
            $countries = json_decode($response->getData());
            foreach ($countries->items as $item) {
                $parts = explode('/', $item->href);
                $countryData = json_decode($this->apicall->execute('GET', '/countries/' . $parts[count($parts) - 1], '')->getData());

                $this->srCountryDatas[$countryData->name] = explode('=', base64_decode($countryData->id))[1];
            }
            $this->getSRCountries(++$page, 200);
        }
        return $this->srCountryDatas;
    }
}