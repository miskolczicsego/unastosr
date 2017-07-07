<?php

namespace ShopMoves\WebInterfaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ShopMovesWebInterfaceBundle:Default:index.html.twig');
    }
}
