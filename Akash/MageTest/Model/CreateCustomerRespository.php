<?php

namespace Akash\MageTest\Model;

use Akash\MageTest\Api\CreateCustomerRespositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
class CreateCustomerRespository implements CreateCustomerRespositoryInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var AccountManagementInterface
     */
    protected $customerManagement;
    /**
     * @var \Akash\MageTest\Helper\Data
     */
    protected $currHelper;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Data constructor.
     *
     * @param AccountManagementInterface $customerManagement
     * @param \Akash\MageTest\Helper\Data $currHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        AccountManagementInterface $customerManagement,
        \Akash\MageTest\Helper\Data $currHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
    ) {
        $this->customerManagement = $customerManagement;
        $this->currHelper = $currHelper;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }
    /**
     * @inheritDoc
     */
    public function checkCustomerFromEmail($email)
    {
        $websiteId = $this->currHelper->getWebsiteId();
        try {
            $this->customerRepository->get($email, $websiteId);
            return false;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function createCustomer($customerData)
    {
        $websiteId = $this->currHelper->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($customerData['emailaddress']);
        $customer->setFirstname($customerData['fname']);
        $customer->setLastname($customerData['lname']);
        $customer->setPassword(\Akash\MageTest\Helper\Data::PASSWORD_CUSTOMER);
        $customer->save();
        return $customer;
    }
}