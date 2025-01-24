<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * Customer donations minimum price config path
     */
    const XML_PATH_EXCLUDED_INDEXER_IDS = 'SergiiBuinii_customer_donations/general/minimum_price';

    /**
     * Retrieve customer donations minimum price
     *
     * @param null|int|string $storeId
     * @return float
     */
    public function getMinimumPrice($storeId = null)
    {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_EXCLUDED_INDEXER_IDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
