<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class Handler
 */
class Handler extends Base
{
    /**
     * @var string $fileName
     */
    protected $fileName = '/var/log/ba_feed.log';

    /**
     * @var int $loggerType
     */
    protected $loggerType = Logger::INFO;

    /**
     * Class constructor.
     *
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystem
     * @param string $filePath
     */
    public function __construct(
        DriverInterface $filesystem,
        $filePath = null
    ) {
        parent::__construct($filesystem, $filePath);
        $format = "[%datetime%] %message% %context% %extra%\n";

        $this->setFormatter(new \Monolog\Formatter\LineFormatter($format, null, true, true));
    }
}
