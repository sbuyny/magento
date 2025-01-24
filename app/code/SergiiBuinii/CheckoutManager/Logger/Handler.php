<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * CheckoutManager Logger Class Handler
 */
class Handler extends Base
{
    /**
     * @var string $fileName
     */
    protected $fileName = '/var/log/checkoutmanager.log';

    /**
     * @var int $loggerType
     */
    protected $loggerType = Logger::INFO;

    /**
     * Handler constructor.
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystem
     * @param string $filePath
     * @throws \Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        $filePath = null
    ) {
        parent::__construct($filesystem, $filePath);

        $this->setFormatter(new LineFormatter(
            "[%datetime%] %message% %context% %extra%\n",
            null,
            true,
            true
        ));
    }
}
