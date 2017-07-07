<?php

namespace ShopMoves\DownloadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ShopMovesDownloadBundle:Default:index.html.twig');
    }
}
