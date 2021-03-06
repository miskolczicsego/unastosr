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
     * @var ContainerInterface $container
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function start()
    {

        $start = $this->startTimer();

        $this->container->get('shopmoves.unasmigration.api.config_provider')->setConfig();

        $migrationList = $this->getListOfMigrations();

        if (!empty($migrationList)) {
            foreach ($migrationList as $migration) {
                $this->container->get($migration)->migrate();
            }
        }

        $elapsedTime = $this->getElapsedTimeFromStart($start);

        $this->logTimeNeeded($elapsedTime);
    }

    public function getListOfMigrations()
    {
        return [
//            'customer_group_migration',
//            'customer_migration',
//            'customer_address_migration',
//            'newsletter_migration',
//            'list_attribute_migration',
//            'list_attribute_description_migration',
//            'list_attribute_value_migration',
//            'list_attribute_value_description_migration',
//            'product_class_migration',
//            'attribute_to_product_class',
//            'product_migration',
//            'product_description_migration',
            'product_url_alias_migration',
//            'child_parent_migration',
//            'product_to_list_attribute_migration',
//            'product_related_migration',
//            'product_special_price_migration',
//            'product_images_migration',
//            'product_option_migration',
//            'product_option_description_migration',
//            'product_option_value_migration',
//            'product_option_value_description_migration',
//            'category_migration',
//            'category_description_migration',
//            'category_to_product_migration',
        ];
    }

    public function logTimeNeeded($time)
    {
        $logger = $this->container->get('monolog.logger.unasmigration');

        $logger->info('End of migration', [
            'time' => number_format($time, 2, '.', ' ') . ' Sec'
        ]);
    }

    public function startTimer()
    {
        return microtime(true);
    }

    public function getElapsedTimeFromStart($start)
    {
        return microtime(true) - $start;
    }
}