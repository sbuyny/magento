<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Partner;

use SergiiBuinii\PartnerFeed\Model\Partner;
use SergiiBuinii\PartnerFeed\Controller\Adminhtml\MassPartner;

/**
 * Class MassEnable
 */
class MassEnable extends MassPartner
{
    /**
     * Disable status
     *
     * @var int
     */
    protected $statusCode = Partner::STATUS_ENABLED;

    /**
     * Retrieve success message after action
     *
     * @return string
     */
    protected function getSuccessMsg()
    {
        return 'A total of %1 record(s) have been enabled.';
    }
}
