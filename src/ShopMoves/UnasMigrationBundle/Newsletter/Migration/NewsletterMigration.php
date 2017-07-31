<?php

namespace ShopMoves\UnasMigrationBundle\Newsletter\Migration;

use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use ShopMoves\UnasMigrationBundle\Newsletter\Provider\NewsletterDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.31.
 * Time: 8:54
 */
class NewsletterMigration extends BatchMigration
{
    protected $subscriberUri = 'newsletterSubscribers';

    public function __construct(
        NewsletterDataProvider $newsletterDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container)
    {
        parent::__construct($newsletterDataProvider, $apiCall, $container);
    }

    public function process($subscriber)
    {
        $subscriberData['firstname'] = isset($subscriber['name']) ? $subscriber['name']['firstname'] : '';
        $subscriberData['lastname'] = isset($subscriber['name']) ? $subscriber['name']['lastname'] : '';
        $subscriberData['email'] = $subscriber['email'];
        $subscriberData['dateSubscribed'] = $subscriber['date'];

        $this->addToBatchArray($this->subscriberUri, '' , $subscriberData);
    }
}