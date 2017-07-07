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
            ->add("password", TextType::class, [
                "label" => "password",
                "data" => "22aa6c9523fdeeaf5b6431c6f2213d8a"
            ])
            ->add("shopid", TextType::class, [
                "label" => "Shop ID",
                "data" => "54927"

            ])
            ->add("auth", TextType::class, [
                "label" => "Authentication code",
                "data" => "a35f40d18d"
            ])
            ->add("save", SubmitType::class, [
                "label" => "Go!!!"
            ]);
        $srFormDecorator = new ShoprenterDecorator();
        $form = $srFormDecorator->addSrFields($form)->getForm();
        return $this->render('@ShopMovesWebInterface/Unas/index.html.twig', ["form" => $form->createView()]);
    }

    public function startAction(Request $request)
    {
        $post = $request->request->get("form");
        $migrationId = $post["username"] . Date("ymdHis");
        /** @var UnasDownloader $unasDownloader */
        $unasDownloader = $this->get("unas_downloader");
        $unasDownloader->setMigrationId($migrationId);
        $unasDownloader->setConfig(
            $post["username"],
            $post["password"],
            $post["shopid"],
            $post["auth"],
            $this->getParameter("kernel.cache_dir")
        );
        $unasDownloader->download();

//        return new RedirectResponse($this->generateUrl("shop_moves_unas_webinterface_home"));
    }
}