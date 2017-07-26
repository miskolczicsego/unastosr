<?php
namespace ShopMoves\UnasMigrationBundle\Migration;
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

    public function start()
    {
        if(file_exists('status.log')) {

            unlink('status.log');
        }
        if(file_exists('api_send_status.log')) {

            unlink('api_send_status.log');
        }
        $start = microtime(true);
//        $timeStamp = date('YmdHis');
//        $currentMigrationId = key($this->migrations);
        $this->container->get('shopmoves.unasmigration.api.config_provider')->setConfig();


//        $this->container->get('customer_group_migration')->migrate($timeStamp);
//        $this->container->get('customer_migration')->migrate($timeStamp);
//        $this->container->get('customer_address_migration')->migrate($timeStamp);
        $this->container->get('list_attribute_migration')->migrate();
        $this->container->get('list_attribute_description_migration')->migrate();
        $this->container->get('list_attribute_value_migration')->migrate();
        $this->container->get('list_attribute_value_description_migration')->migrate();
        $this->container->get('product_class_migration')->migrate();
        $this->container->get('attribute_to_product_class')->migrate();
        $this->container->get('product_migration')->migrate();
        $this->container->get('product_description_migration')->migrate();
        $this->container->get('product_related_migration')->migrate();
        $this->container->get('product_to_list_attribute_migration')->migrate();
        $this->container->get('product_special_price_migration')->migrate();
        $this->container->get('product_url_alias_migration')->migrate();
        $this->container->get('product_images_migration')->migrate();
        $this->container->get('product_option_migration')->migrate();
        $this->container->get('product_option_description_migration')->migrate();
        $this->container->get('product_option_value_migration')->migrate();
        $this->container->get('product_option_value_description_migration')->migrate();
//        $this->container->get('category_migration')->migrate();
        $time = microtime(true) - $start;
        file_put_contents('status.log', 'End of migration ' . PHP_EOL . 'Time needed: ' . number_format($time, 2, '.', ' ') . ' Sec' .PHP_EOL , FILE_APPEND);
        dump( number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ') . ' MB');


//        $this->migrations[$currentMigrationId]->migrate();
    }
}