<?php
namespace ShopMoves\UnasMigrationBundle\Customer\Migration;
use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Customer\Provider\CustomerDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 10:38
 */
class CustomerMigration extends BatchMigration
{

    protected $customerUri = '/customers/';



    /**
     * CustomerMigration constructor.
     * @param CustomerDataProvider $dataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     */
    public function __construct(CustomerDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {

        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($customerData)
    {
        $nameParts = $this->container->get('name_helper')->separate($customerData->Contact->Name);

        $customerOuterId = $this->getOuterId($customerData->Id);
        $data['id'] =  $customerOuterId;
        $data['firstname'] = $nameParts['firstname'];
        $data['lastname'] = $nameParts['lastname'];
        $data['email'] = $customerData->Email;
        $data['telephone'] = $this->getCustomerPhoneNumber($customerData);
        $data['password'] = password_hash($customerData->Email, PASSWORD_DEFAULT);
        $data['newsletter'] = $customerData->Newsletter->Subscribed == "yes" ? 1 : 0;
        $data['status'] = $customerData->Authorize->Customer == "yes" ? 1 : 0;
        $data['customerGroup'] = [
            'id' => isset($customerData->Group->Id) ? base64_encode($customerData->Group->Id) : $this->getDefaultCustomerGroupOuterId()
        ];

        $this->addToBatchArray($this->customerUri, $customerOuterId, $data);
    }

    public function getCustomerPhoneNumber($data)
    {
        if (is_string($data->Contact->Mobile)) {
            return $data->Contact->Mobile;
        }
        return $data->Contact->Phone;
    }

    public function getOuterId($id)
    {
        return base64_encode($id);
    }

    //fixen az alapértelmezett csoport
    public function getDefaultCustomerGroupOuterId()
    {
        return base64_encode('customerGroup-customer_group_id=8');
    }
}