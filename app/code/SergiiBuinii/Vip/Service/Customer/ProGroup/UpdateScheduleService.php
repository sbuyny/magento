<?php

namespace SergiiBuinii\Vip\Service\Customer\ProGroup;

use SergiiBuinii\JobManager\Model\JobFactory;
use SergiiBuinii\Vip\Helper\Config;
use SergiiBuinii\Vip\Helper\Data;
use SergiiBuinii\Vip\Job\DowngradeCustomerGroup;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class UpdateScheduleService
{
    /**
     * Job chunk for customer update
     */
    const JOB_CHUNK = 30;
    
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    
    /**
     * @var \SergiiBuinii\Vip\Helper\Config
     */
    protected $config;
    
    /**
     * @var \SergiiBuinii\JobManager\Model\JobFactory
     */
    private $jobFactory;
    
    /**
     * UpdateScheduleService constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \SergiiBuinii\Vip\Helper\Config $config
     * @param \SergiiBuinii\JobManager\Model\JobFactory $jobFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        TimezoneInterface $timezone,
        Config $config,
        JobFactory $jobFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->timezone = $timezone;
        $this->config = $config;
        $this->jobFactory = $jobFactory;
    }
    
    /**
     * Search for customers with expired vip group and schedule update.
     *
     * @return $this
     */
    public function execute()
    {
        if (!$this->config->checkExpirationDate()) {
            return $this;
        }
        $customers = $this->getCustomersWithExpiredVipGroup();
        if (!empty($customers)) {
            $this->scheduleUpdate($customers);
        }
        return $this;
    }
    
    /**
     * Fetch customers with expired vip group
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    private function getCustomersWithExpiredVipGroup()
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria
            ->addFilter(
                Data::VIP_CUSTOMER_ATTR_EXP_DATE,
                $this->timezone->date()->format(DateTime::DATE_PHP_FORMAT),
                'lteq'
            )
            ->addFilter(
                CustomerInterface::GROUP_ID,
                array_keys($this->config->getDowngradeMapping()),
                'in'
            );
        
        $searchResult = $this->customerRepository->getList(
            $searchCriteria->create()
        );
        return $searchResult->getItems();
    }
    
    /**
     * Schedule update
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface[] $customers
     */
    private function scheduleUpdate($customers)
    {
        $bunch = [];
        foreach ($customers as $customer) {
            $bunch[] = $customer->getId();
            if (count($bunch) == self::JOB_CHUNK) {
                $this->createJob($bunch);
                $bunch = [];
            }
        }
        if (!empty($bunch)) {
            $this->createJob($bunch);
        }
    }
    
    /**
     * Create job
     *
     * @param int[]
     * @return void
     */
    private function createJob($ids)
    {
        $this->jobFactory->create()
            ->setType(DowngradeCustomerGroup::class)
            ->setDetails([DowngradeCustomerGroup::JOB_CUSTOMER_IDS => $ids])
            ->setPriority(DowngradeCustomerGroup::JOB_PRIORITY)
            ->setSchedule($this->getScheduleDate())
            ->save();
    }
    
    /**
     * Get schedule date for customer group update
     *
     * Schedule job execution at 8:00am
     *
     * @return string
     */
    private function getScheduleDate()
    {
        return $this->timezone->convertConfigTimeToUtc(
            $this->timezone->date()
                ->setTime("8", "0")
                ->format(DateTime::DATETIME_PHP_FORMAT)
        );
    }
}
