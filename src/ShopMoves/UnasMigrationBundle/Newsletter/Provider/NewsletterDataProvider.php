<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.31.
 * Time: 8:56
 */

namespace ShopMoves\UnasMigrationBundle\Newsletter\Provider;


use ShopMoves\UnasMigrationBundle\Helper\NameHelper;
use ShopMoves\UnasMigrationBundle\Provider\DataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsletterDataProvider extends DataProvider
{

    protected $fileName = 'kisciponewsletter';

    protected $extension = 'json';

    /** @var ContainerInterface $container */
    protected $container;

    /** @var  NameHelper $nameHelper */
    protected $nameHelper;

    protected $dateTime;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->nameHelper = $this->container->get('customer_name_helper');
        $this->dateTime = new \DateTime();
        parent::__construct($container);
    }

    public function getData()
    {
        $json = $this->getFileContentAsJson();

        $subsrcibers = $json->Subscribers->Subscriber;

        $subscriberData = [];

        foreach ($subsrcibers as $subscriber) {

            $subscriberData[$subscriber->Email] = [
                'email' => $subscriber->Email,
                'status' => $subscriber->Authorized == 'yes' ? 1 : 0,
                'date' => $this->getSubscriptionTime($subscriber),
            ];

            if (isset($subscriber->Name)) {
                $name = $this->nameHelper->separate($subscriber->Name);
                $subscriberData[$subscriber->Email]['name'] = [
                    'firstname' => $name['firstname'],
                    'lastname' => $name['lastname'],
                ];
            }


        }

        return $subscriberData;
    }

    public function getSubscriptionTime($subscribtion)
    {
        if (isset($subscribtion->Time)) {
            $this->dateTime->setTimestamp($subscribtion->Time);
        } else {
            return '0000-00-00 00:00:00';
        }
        return $this->dateTime->format('Y-m-d H:i:s');
    }
}