<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use SergiiBuinii\PartnerFeed\Helper\Config as ConfigHelper;
use SergiiBuinii\PartnerFeed\Logger\DebugLogger;

/**
 * Class Debugger
 */
class Debugger extends \Magento\Framework\DataObject
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \SergiiBuinii\PartnerFeed\Helper\Config
     */
    protected $configHelper;

    /**
     * Debugger constructor.
     * @param \SergiiBuinii\PartnerFeed\Logger\DebugLogger $logger
     * @param \SergiiBuinii\PartnerFeed\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        DebugLogger $logger,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->logger = $logger;
        $this->configHelper = $configHelper;
    }

    /**
     * Check if debugging is enabled
     *
     * @return bool
     */
    public function isDebugFlag()
    {
        return $this->configHelper->isLoggingEnabled();
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     * @param bool $isException
     * @return void
     */
    public function debugData($debugData, $isException = false)
    {
        if ($this->isDebugFlag()) {
            if (!$isException) {
                $debugData = var_export($debugData, true);
            }
            $this->logger->debug($debugData);
        }
    }

    /**
     * Debug errors
     *
     * @param array $errors
     * @return void
     */
    public function debugErrors($errors)
    {
        if ($this->isDebugFlag() && count($errors) > 0) {
            foreach ($errors as $error) {
                $this->debugData($error, true);
            }
        }
    }
}
