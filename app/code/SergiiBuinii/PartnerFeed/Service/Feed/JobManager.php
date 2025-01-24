<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Service\Feed;

use SergiiBuinii\PartnerFeed\Job\Feed\Import;
use SergiiBuinii\JobManager\Helper\Config as JobConfig;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use SergiiBuinii\JobManager\Model\JobFactory;

/**
 * Class JobManager
 */
class JobManager
{
    /**
     * @var \SergiiBuinii\JobManager\Model\JobFactory
     */
    private $jobFactory;

    /**
     * JobManager constructor
     *
     * @param \SergiiBuinii\JobManager\Model\JobFactory $jobFactory
     */
    public function __construct(JobFactory $jobFactory)
    {
        $this->jobFactory = $jobFactory;
    }

    /**
     * Create update partner feeds job
     *
     * @param object $data
     * @return void
     *
     * @SuppressWarnings("unused")
     */
    public function createFeedJob($data)
    {
        $this->jobFactory->create()
            ->setType(Import::class)
            ->setSource(sprintf('Partner Id: #%d', $data->getData(PartnerInterface::ENTITY_ID)))
            ->setPriority(JobConfig::JOB_PRIORITY_LOW)
            ->setDetails([PartnerInterface::ENTITY_ID => $data->getData(PartnerInterface::ENTITY_ID)])
            ->save();
    }
}
