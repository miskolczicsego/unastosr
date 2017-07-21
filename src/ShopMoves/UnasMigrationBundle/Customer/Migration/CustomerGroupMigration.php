<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 13:04
 */

namespace ShopMoves\UnasMigrationBundle\Customer\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Customer\Provider\CustomerDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomerGroupMigration extends BatchMigration
{

    protected $groups  = [];

    protected $customerGroupUri = 'customerGroups';

    public function __construct(CustomerDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($customer)
    {
        if(isset($customer->Group) && !array_key_exists($customer->Group->Name, $this->groups)) {
            $this->groups[$customer->Group->Name] = $customer->Group;

            $groupOuterId = $this->getOuterId($customer->Group->Id);

            $data['id'] = $groupOuterId;
            $data['name'] = $customer->Group->Name;

            $this->addToBatchArray($this->customerGroupUri, $groupOuterId, $data);
        }
    }

    public function getOuterId($data)
    {
        return base64_encode($data);
    }
}