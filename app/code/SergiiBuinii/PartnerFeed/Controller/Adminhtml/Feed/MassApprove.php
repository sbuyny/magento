<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Feed;

use SergiiBuinii\PartnerFeed\Model\Feed;

/**
 * Class MassApprove
 */
class MassApprove extends \SergiiBuinii\PartnerFeed\Controller\Adminhtml\Mass
{
    /**
     * Approve
     *
     * @var int
     */
    protected $statusCode = Feed::STATUS_APPROVED;

    /**
     * Retrieve success message after action
     *
     * @return string
     */
    protected function getSuccessMsg()
    {
        return 'A total of %1 record(s) have been approved.';
    }
}
