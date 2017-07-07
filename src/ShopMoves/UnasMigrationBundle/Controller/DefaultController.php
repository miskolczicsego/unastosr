<?php

namespace ShopMoves\UnasMigrationBundle\Controller;

use ShopMoves\UnasMigrationBundle\Config\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @var Config
     */
    protected $config;

    public function indexAction()
    {
        return new Response();
    }

    public function unasGetAction()
    {

        $method = 'getProduct';
        $soapServer = "https://api.unas.eu/shop/?wsdl";
        ini_set("soap.wsdl_cache_enabled", "0");

        $authOwn = [
            "Username" => "miskolczicsego",
            "PasswordCrypt" => "2d51e31bcde70fa83ece051e3fd99628",
            "ShopId" => 45687,
            "AuthCode" => "2a4fbcd506",
        ];

        $authKiscipo = [
            "Username" => "kiscipo.hu",
            "PasswordCrypt" => "22aa6c9523fdeeaf5b6431c6f2213d8a",
            "ShopId" => 54927,
            "AuthCode" => "a35f40d18d",
        ];

        $client = new \SoapClient($soapServer);

        try{
            $params = [
                "ContentType" => "full",
                "Sku" => "534669"
            ];

            $response = $client->{$method}($authKiscipo, $params);

            return new Response("<pre>" . (htmlspecialchars($response) . "</pre>"));

        } catch (\SoapFault $err) {
            $err->getMessage();
        }
        return new Response("dséadas");
    }
}
