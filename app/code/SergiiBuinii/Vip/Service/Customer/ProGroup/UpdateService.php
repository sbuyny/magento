<?php

namespace SergiiBuinii\Vip\Service\Customer\ProGroup;

use SergiiBuinii\Vip\Helper\Config;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class UpdateService
{
    /**
     * @var \SergiiBuinii\Vip\Helper\Config
     */
    private $config;
    
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    
    /**
     * UpdateService constructor.
     *
     * @param \SergiiBuinii\Vip\Helper\Config $config
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Config $config,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->config = $config;
        $this->customerRepository = $customerRepository;
    }
    
    /**
     * Update customer
     *
     * @param array $ids
     * @throws LocalizedException
     * @return void
     */
    public function execute(array $ids)
    {
        foreach ($ids as $id) {
            $customer = $this->getCustomer($id);
            if (!$customer) {
                continue;
            }
            $this->downgradeCustomerGroup($customer);
        }
    }
    
    /**
     * Get customer
     *
     * @param $id
     * @return false|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function getCustomer($id)
    {
        try {
            $customer =  $this->customerRepository->getById($id);
        } catch (LocalizedException $e) {
            $customer = false;
        }
        return $customer;
    }
    
    /**
     * Downgrade customer group
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @throws LocalizedException
     */
    private function downgradeCustomerGroup($customer)
    {
        $newGroup = $this->config->getDowngradeMapping()[$customer->getGroupId()] ?? false;
        if (false === $newGroup) {
            throw new LocalizedException(
                __(
                    'Mapping for group id %1 does not set. Customer id %2.',
                    $customer->getGroupId(),
                    $customer->getId()
                )
            );
        }
        $customer->setGroupId($newGroup);
        $this->customerRepository->save($customer);
    }
}
