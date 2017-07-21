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

    /**
     * CustomerMigration constructor.
     * @param CustomerDataProvider $dataProvider
     * @param ApiCall $apiCall
     * @param ContainerInterface $container
     * @param CustomerGroupHelper $customerGroupHelper
     */
    public function __construct(
        CustomerDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container,
        CustomerGroupHelper $customerGroupHelper
    ) {
        $this->customerGroupHelper = $customerGroupHelper;
        parent::__construct($dataProvider, $apiCall, $container);
    }

    /**
     * @param object $customerData
     */
    public function process($customerData)
    {
        $nameParts = $this->container->get('customer_name_helper')->separate($customerData->Contact->Name);

        $customerOuterId = $this->getCustomerOuterId($customerData->Id);
        $data['id'] =  $customerOuterId;
        $data['firstname'] = $nameParts['firstname'];
        $data['lastname'] = $nameParts['lastname'];
        $data['email'] = $customerData->Email;
        $data['telephone'] = $this->getCustomerPhoneNumber($customerData);
        $data['password'] = $this->generatePasswordFromEmail($customerData->Email);
        $data['newsletter'] = $customerData->Newsletter->Subscribed == "yes" ? 1 : 0;
        $data['status'] = $customerData->Authorize->Customer == "yes" ? 1 : 0;
        $data['customerGroup'] = [
            'id' => isset($customerData->Group->Id) ?
                $this->getCustomerGroupOuterId($customerData->Group->Id) :
                $this->customerGroupHelper->getDefaultSRCustomerGroup()
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

    /**
     * @param string $data
     * @return string
     */
    public function getCustomerOuterId($data)
    {
        return base64_encode('customer-customer_id=' . $data);
    }

    /**
     * @param String $data
     * @return string
     */
    public function getCustomerGroupOuterId($data)
    {
        return base64_encode('customerGroup-customer_group_id=' . $data);
    }

    /**
     * @param $email
     * @return bool|false|string
     */
    public function generatePasswordFromEmail($email)
    {
        return password_hash($email, PASSWORD_DEFAULT);
    }
}