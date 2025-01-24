<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Cron\Feed;

use SergiiBuinii\PartnerFeed\Service\ScheduleService;

/**
 * Class Import
 */
class Import
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Service\ScheduleService
     */
    private $scheduleService;

    /**
     * Import constructor
     * @param \SergiiBuinii\PartnerFeed\Service\ScheduleService $scheduleService
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Created job for update all active partners feeds
     */
    public function execute()
    {
        $this->scheduleService->execute();
    }
}
