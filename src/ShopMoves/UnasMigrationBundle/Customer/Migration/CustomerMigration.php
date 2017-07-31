<?php
namespace ShopMoves\UnasMigrationBundle\Customer\Migration;
use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Customer\Provider\CustomerDataProvider;
use ShopMoves\UnasMigrationBundle\Helper\CustomerGroupHelper;
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

    /**
     *
     * @var string $customerUri
     *
     **/
    protected $customerUri = 'customers';

    /**
     * @var CustomerGroupHelper $customerGroupHelper
     */
    protected $customerGroupHelper;

    protected $customerDataProvider;

    /**
     * CustomerMigration constructor.
     * @param CustomerDataProvider $customerDataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     * @param CustomerGroupHelper $customerGroupHelper
     */
    public function __construct(
        CustomerDataProvider $customerDataProvider,
        ApiCall $apiCall,
        ContainerInterface $container,
        CustomerGroupHelper $customerGroupHelper
    ) {
        $this->customerGroupHelper = $customerGroupHelper;
        $this->customerDataProvider = $customerDataProvider;
        parent::__construct($customerDataProvider, $apiCall, $container);
    }

    /**
     * @param object $customerData
     */
    public function process($customerData)
    {
        $nameParts = $this->container->get('customer_name_helper')->separate($customerData->Contact->Name);

        $customerOuterId = $this->customerDataProvider->getCustomerOuterId($customerData->Id);
        $data['id'] =  $customerOuterId;
        $data['firstname'] = $nameParts['firstname'];
        $data['lastname'] = $nameParts['lastname'];
        $data['email'] = $customerData->Email;
        $data['telephone'] = $this->customerDataProvider->getCustomerPhoneNumber($customerData);
        $data['password'] = $this->customerDataProvider->generatePasswordFromEmail($customerData->Email);
        $data['newsletter'] = $customerData->Newsletter->Subscribed == "yes" ? '1' : '0';
        $data['status'] = $customerData->Authorize->Customer == "yes" ? 1 : 0;
        $data['approved'] = 1;
        $data['customerGroup'] = [
            'id' => isset($customerData->Group->Id) ?
                $this->customerDataProvider->getCustomerGroupOuterId($customerData->Group->Id) :
                $this->customerGroupHelper->getDefaultSRCustomerGroup()
        ];

        $this->addToBatchArray($this->customerUri, $customerOuterId, $data);
    }
}