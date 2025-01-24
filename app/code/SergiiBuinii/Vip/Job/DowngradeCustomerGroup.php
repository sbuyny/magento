<?php

namespace SergiiBuinii\Vip\Job;

use SergiiBuinii\JobManager\Api\JobRunInterface;
use SergiiBuinii\JobManager\Api\JobRunResultInterfaceFactory;
use SergiiBuinii\JobManager\Helper\Config as JobConfig;
use SergiiBuinii\Vip\Service\Customer\ProGroup\UpdateService;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class DowngradeCustomerGroup implements JobRunInterface
{
    /**
     * Job priority level
     */
    const JOB_PRIORITY = JobConfig::JOB_PRIORITY_LOWEST;
    
    /**
     * @var \SergiiBuinii\JobManager\Api\JobRunResultInterfaceFactory
     */
    protected $jobRunResultFactory;
    
    /**
     * Job inventory data filed
     */
    const JOB_CUSTOMER_IDS = 'customer_ids';
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \SergiiBuinii\Vip\Service\Customer\ProGroup\UpdateService
     */
    protected $updateService;
    
    /**
     * DowngradeCustomerGroup constructor.
     *
     * @param \SergiiBuinii\JobManager\Api\JobRunResultInterfaceFactory $jobRunResultFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \SergiiBuinii\Vip\Service\Customer\ProGroup\UpdateService $updateService
     */
    public function __construct(
        JobRunResultInterfaceFactory $jobRunResultFactory,
        LoggerInterface $logger,
        UpdateService $updateService
    ) {
        $this->jobRunResultFactory = $jobRunResultFactory;
        $this->logger = $logger;
        $this->updateService = $updateService;
    }
    
    /**
     * Run job
     *
     * @param \SergiiBuinii\JobManager\Api\Data\JobInterface $job
     * @return \SergiiBuinii\JobManager\Api\JobRunResultInterface
     * @throws \Exception
     */
    public function run($job)
    {
        /** @var \SergiiBuinii\JobManager\Api\JobRunResultInterface $result */
        $result = $this->jobRunResultFactory->create();
        $details = $job->getDetails();
        try {
            $this->updateService->execute($details[self::JOB_CUSTOMER_IDS] ?? []);
        } catch (LocalizedException $e) {
            return $result
                ->setDone(false)
                ->setErrors([$e->getMessage()]);
        } catch (\Exception $e) {
            $this->logger->error($e);
            return $result
                ->setDone(false)
                ->setErrors([$e->getMessage()]);
        }
        return $result->setDone(true)->setSuccess(_('Customer group update has been done successfully.'));
    }
}
