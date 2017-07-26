<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.21.
 * Time: 14:42
 */

namespace ShopMoves\UnasMigrationBundle\Address\Resolver;


use Symfony\Component\DependencyInjection\ContainerInterface;

class CountryResolver
{

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    public function resolve($address, $srCountries)
    {
        return array_key_exists($address, $srCountries)
            ? base64_encode(
            'country-country_id='.$srCountries[$address]
        )
            : base64_encode('country-country_id='.$srCountries['Magyarország']);
    }
}