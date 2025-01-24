<?php

namespace SergiiBuinii\Vip\Cron;

use SergiiBuinii\Vip\Service\Customer\ProGroup\UpdateScheduleService;

class ExpirationDateCheck
{
    /**
     * @var \SergiiBuinii\Vip\Service\Customer\ProGroup\UpdateScheduleService
     */
    private $updateScheduleService;
    
    /**
     * ExpirationDateCheck constructor.
     * @param \SergiiBuinii\Vip\Service\Customer\ProGroup\UpdateScheduleService $updateScheduleService
     */
    public function __construct(UpdateScheduleService $updateScheduleService)
    {
        $this->updateScheduleService = $updateScheduleService;
    }
    
    /**
     * Fetch customers with expired date and schedule group update
     */
    public function execute()
    {
        $this->updateScheduleService->execute();
    }
}
