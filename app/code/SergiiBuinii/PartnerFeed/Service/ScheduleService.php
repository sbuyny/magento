<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Service;

use SergiiBuinii\PartnerFeed\Helper\Config;
use SergiiBuinii\PartnerFeed\Model\Debugger;
use Magento\Framework\Exception\LocalizedException;
use SergiiBuinii\JobManager\Model\JobFactory;
use SergiiBuinii\PartnerFeed\Service\Feed\JobManager;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use SergiiBuinii\PartnerFeed\Model\Partner;
use Magento\Framework\Api\SearchCriteriaBuilder;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;

/**
 * Class ScheduleService
 */
class ScheduleService
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\Debugger
     */
    protected $debugger;
    
    /**
     * @var \SergiiBuinii\PartnerFeed\Service\Feed\JobManager
     */
    protected $jobManager;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * AbstractService constructor
     *
     * @param \SergiiBuinii\PartnerFeed\Helper\Config $configHelper
     * @param \SergiiBuinii\PartnerFeed\Model\Debugger $debugger
     * @param \SergiiBuinii\PartnerFeed\Service\Feed\JobManager $jobManager
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        Config $configHelper,
        Debugger $debugger,
        JobManager $jobManager,
        PartnerRepository $partnerRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->configHelper = $configHelper;
        $this->debugger = $debugger;
        $this->jobManager = $jobManager;
        $this->partnerRepository = $partnerRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * Update order data
     *
     * @return bool
     */
    public function execute()
    {
        if (!$this->configHelper->isEnable()) {
            return false;
        }

        try {
            $this->debugger->debugData('Started create jobs for partner feeds ...');

            foreach ($this->getPartners() as $partner) {
                try {
                    $this->jobManager->createFeedJob($partner);
                } catch (LocalizedException $e) {
                    $this->debugger->debugData($e->getMessage());
                }
            }

            $this->debugger->debugData('Created jobs for partnet feeds ended');
        } catch (\Exception $e) {
            $this->debugger->debugData($e->getMessage());
        }
    }

    /**
     * Get enabled partner list
     *
     * @return mixed
     */
    private function getPartners()
    {
        $criteria = $this->criteriaBuilder
            ->addFilter(PartnerInterface::STATUS, Partner::STATUS_ENABLED)
            ->create();
        $items = $this->partnerRepository->getList($criteria)->getItems();
        return $items;
    }
}
