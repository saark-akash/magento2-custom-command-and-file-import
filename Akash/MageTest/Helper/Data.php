<?php

namespace Akash\MageTest\Helper;

class Data
{
    public const PASSWORD_CUSTOMER = "Admin123";
    public const STORE_CODE = "default";
    /**
     * @var storeRepository
     */
    protected $storeRepository;
    /**
     * @var storeManager
     */
    private $storeManager;
    /**
     * Constructor
     * 
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeRepository = $storeRepository;
        $this->storeManager = $storeManager;
    }
    /**
     * Get store
     * @return object
     */
    public function getStore()
    {
        try {
            return $this->storeRepository->get(self::STORE_CODE);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->storeManager->getStore();
        }
    }
    /**
     * Get store website id
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getStore()->getWebsiteId();
    }
}