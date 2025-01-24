<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Config\Backend\Feed;

use SergiiBuinii\PartnerFeed\Model\Config\Backend\Frequency;

/**
 * Class Import
 */
class Import extends Frequency
{
    protected $cronStringPath = 'crontab/feed/jobs/feed_import/schedule/cron_expr';
}
