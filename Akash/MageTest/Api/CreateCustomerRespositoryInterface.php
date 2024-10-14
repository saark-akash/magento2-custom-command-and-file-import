<?php

namespace Akash\MageTest\Api;

/**
 * Interface RepositoryInterface must be implemented in new model
 */
interface CreateCustomerRespositoryInterface
{
    /**
     * Retrieve customer information on email
     *
     * @param string email
     * @return bool
     */
    public function checkCustomerFromEmail($email);

    /**
     * Create customer
     *
     * @param array $customerData
     * @return object
     */
    public function createCustomer($customerData);
}
