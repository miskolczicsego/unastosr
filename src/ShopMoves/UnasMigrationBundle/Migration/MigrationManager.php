<?php
namespace ShopMoves\UnasMigrationBundle\Migration;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 16:31
 */
class MigrationManager
{
    /**
     * @var BatchMigration[]
     */
    protected $migrations;

    /**
     * @var ContainerAwareInterface $container
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

//    public function addMigration(BatchMigration $migration)
//    {
//        $this->migrations[$migration->getMigrationId()] = $migration;
//    }

    public function start()
    {
//        unlink('responseData.log');
        $start = microtime(true);
//        $currentMigrationId = key($this->migrations);
        $this->container->get('shopmoves.unasmigration.api.config_provider')->setConfig();


//        $this->container->get('customer_group_migration')->migrate();
//        $this->container->get('customer_migration')->migrate();
//        $this->container->get('customer_address_migration')->migrate();
        //TODO: a képletöltő/feltöltő még szar
//        $this->container->get('product_image_migration')->migrate();
//        $this->container->get('product_migration')->migrate();
//        $this->container->get('product_description_migration')->migrate();
//        $this->container->get('product_class_migration')->migrate();
//        $this->container->get('product_special_price_migration')->migrate();
//        $this->container->get('product_url_alias_migration')->migrate();
        $this->container->get('product_option_migration')->migrate();

//        $this->container->get('category_migration')->migrate();

        $time = microtime(true) - $start;
        dump(number_format($time, 2, '.', ' ') . ' Sec');
        dump( number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ') . ' MB');


//        $this->migrations[$currentMigrationId]->migrate();
    }
}