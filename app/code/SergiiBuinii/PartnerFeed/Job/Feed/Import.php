<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Job\Feed;

use SergiiBuinii\JobManager\Api\JobRunResultInterface;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use SergiiBuinii\PartnerFeed\Model\Debugger;
use SergiiBuinii\PartnerFeed\Service\Feed\FeedService;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;

/**
 * Class Import
 */
class Import
{
    /**
     * @var \SergiiBuinii\JobManager\Api\JobRunResultInterface
     */
    private $jobRunResult;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\Debugger
     */
    private $debugger;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * @var \SergiiBuinii\PartnerFeed\Service\Feed\FeedService
     */
    private $feedService;

    /**
     * @var array $messages
     */
    private $messages = [
        'details_empty'         => 'Job details were empty.',
        'running_job'           => 'Running job %s...',
        'success'               => 'Partner feeds was created.',
    ];

    /**
     * Import constructor.
     * @param \SergiiBuinii\JobManager\Api\JobRunResultInterface $runResultInterface
     * @param \SergiiBuinii\PartnerFeed\Model\Debugger $debugger
     * @param \SergiiBuinii\PartnerFeed\Service\Feed\FeedService $feedService
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     */
    public function __construct(
        JobRunResultInterface $runResultInterface,
        Debugger $debugger,
        FeedService $feedService,
        PartnerRepository $partnerRepository
    ) {
        $this->jobRunResult = $runResultInterface;
        $this->debugger = $debugger;
        $this->feedService = $feedService;
        $this->partnerRepository = $partnerRepository;
    }

    /**
     * Run Job
     *
     * @param string $job
     * @return \SergiiBuinii\JobManager\Api\JobRunResultInterface
     *
     * @SuppressWarnings("unused")
     */
    public function run($job)
    {
        $result = $this->jobRunResult;
        $details = $job->getDetails();
        if (empty($details[PartnerInterface::ENTITY_ID])) {
            return $result->setDone(true)
                ->setErrors([$this->messages['details_empty']]);
        }
        try {
            $this->debugger->debugData(sprintf($this->messages['running_job'], $job->getId()));
            $partner = $this->partnerRepository->getById($details[PartnerInterface::ENTITY_ID]);
            $this->feedService->execute($partner);
        } catch (\Exception $e) {
            $result->setDone(false)->setErrors([$e->getMessage()]);
            return $result;
        }
        $result->setDone(true)->setSuccess($this->messages['success']);
        return $result;
    }
}
