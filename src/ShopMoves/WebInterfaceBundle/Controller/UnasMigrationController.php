<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 12:52
 */

namespace ShopMoves\WebInterfaceBundle\Controller;


use ShopMoves\DownloadBundle\Downloader\UnasDownloader;
use ShopMoves\WebInterfaceBundle\Form\ShoprenterDecorator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnasMigrationController extends Controller
{
    public function indexAction()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl("shop_moves_unas_webinterface_start"))
            ->add("username", TextType::class, [
                "label" => "Username",
                "data" => "kiscipo.hu"
            ])
            ->add("passwordcrypt", TextType::class, [
                "label" => "passwordcrypt",
                "data" => "22aa6c9523fdeeaf5b6431c6f2213d8a"
            ])
            ->add("shopId", TextType::class, [
                "label" => "Shop ID",
                "data" => "54927"

            ])
            ->add("authcode", TextType::class, [
                "label" => "Authentication code",
                "data" => "a35f40d18d"
            ])

            ->add("sr-api-url", TextType::class, [
                "label" => "SR API URL",
                "data" => "http://demo.api.aurora.miskolczicsego"
            ])
            ->add("sr-username", TextType::class, [
                "label" => "SR api user",
                "data" => 'test'
            ])

            ->add("sr-password", TextType::class, [
                "label" => "SR api pass",
                'data' => '2dcd07ef6f3515a5f3a00daba7967fb6'
            ])
            ->add("save", SubmitType::class, [
                "label" => 'Migrate'
            ])
            ->getForm();

        return $this->render('@ShopMovesWebInterface/Unas/index.html.twig', ["form" => $form->createView()]);
    }

    public function startAction(Request $request)
    {
        $post = $request->request->get("form");
        $apiConfigProvider = $this->get('shopmoves.unasmigration.api.config_provider');
        $apiConfigProvider->setConfig($post);
        $apiCall = $this->container->get('shopmoves.unasmigration.api.apicall');
//        $downloaderUrl = "{$this->getParameter("unas-downloader-url")}/start";

       return $this->redirectToRoute('shop_moves_unas_migration_start');
//        $this->httpPost($downloaderUrl, $post);


//        return new RedirectResponse($this->generateUrl("shop_moves_unas_webinterface_home"));
    }

    public function downloadImagesAction()
    {
        $downloader = $this->container->get('downloader.image_downloader');

        $response = $downloader->download();

        return new Response($response);
    }

    public function httpPost($url, $data)
    {
        $ch = curl_init();

        $postfield = http_build_query($data);

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);

        curl_close ($ch);

        dump($server_output, $error, $info);die;
    }
}