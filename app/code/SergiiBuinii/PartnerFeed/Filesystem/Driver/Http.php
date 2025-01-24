<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Filesystem\Driver;

use Magento\Framework\Filesystem\Driver\Http as OriginHttp;

/**
 * Class Http
 */
class Http extends OriginHttp
{
    /**
     * Scheme distinguisher
     *
     * @var string
     */
    protected $scheme = '';
}
