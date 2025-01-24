<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Logger\Handler;

use \SergiiBuinii\PartnerFeed\Logger\Handler;

/**
 * Class Debug
 */
class Debug extends Handler
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/ba_feed_debug.log';

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;
}
