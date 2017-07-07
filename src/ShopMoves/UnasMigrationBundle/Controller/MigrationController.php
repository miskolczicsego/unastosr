<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 16:30
 */

namespace ShopMoves\UnasMigrationBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MigrationController extends Controller
{
    public function migrateAction()
    {
        $migrationManager = $this->container->get('shopmoves.unasmigration.migration.migration_manager');

        $migrationManager->start();

        return new Response('END', 200);
    }
}