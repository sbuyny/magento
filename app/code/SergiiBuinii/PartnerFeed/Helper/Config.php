<?php
/**
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config
 */
class Config extends AbstractHelper
{

    /**
     * Partner Feed base path
     */
    const XML_BASE_PATH = 'SergiiBuinii_feed/';

    /**
     * Partner Feed enable flag
     */
    const XML_PATH_ENABLE = self::XML_BASE_PATH . 'general/enabled';

    /**
     * Partner Feed API base logging
     */
    const XML_PATH_LOGGING = self::XML_BASE_PATH . 'developer/base_logging';

    /**
     * Price Difference
     */
    const XML_PATH_PRICE_DIFFERENCE = self::XML_BASE_PATH . 'frontend/price_difference';

    /**
     * Check is Partner Feed integration enable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE);
    }

    /**
     * Check if Partner Feed API base logging enabled
     *
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_LOGGING);
    }

    /**
     * Get Price Difference
     *
     * @return float
     */
    public function getPriceDifference()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRICE_DIFFERENCE);
    }
}
