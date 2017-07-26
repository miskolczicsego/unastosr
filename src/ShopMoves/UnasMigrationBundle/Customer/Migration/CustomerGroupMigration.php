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

    protected $customerDatProvider;

    public function __construct(
        CustomerDataProvider $customerDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->customerDatProvider = $customerDataProvider;
        parent::__construct($customerDataProvider, $apiCall, $container);
    }

    public function process($customer)
    {
        if(isset($customer->Group) && !array_key_exists($customer->Group->Name, $this->groups)) {
            $this->groups[$customer->Group->Name] = $customer->Group;

            $groupOuterId = $this->customerDatProvider->getCustomerGroupOuterId($customer->Group->Id);

            $data['id'] = $groupOuterId;
            $data['name'] = $customer->Group->Name;

            $this->addToBatchArray($this->customerGroupUri, $groupOuterId, $data);
        }
    }


}