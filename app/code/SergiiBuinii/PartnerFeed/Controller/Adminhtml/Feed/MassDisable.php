<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Feed;

use SergiiBuinii\PartnerFeed\Model\Feed;

/**
 * Class MassDisable
 */
class MassDisable extends \SergiiBuinii\PartnerFeed\Controller\Adminhtml\Mass
{
    /**
     * Disable status
     *
     * @var int
     */
    protected $statusCode = Feed::STATUS_DISABLED;

    /**
     * Retrieve success message after action
     *
     * @return string
     */
    protected function getSuccessMsg()
    {
        return 'A total of %1 record(s) have been disabled.';
    }
}
