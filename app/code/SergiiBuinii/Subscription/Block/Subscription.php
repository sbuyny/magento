<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Block;

use Magento\Directory\Block\Data;
use Magento\Store\Model\ScopeInterface;

class Subscription extends Data
{
    /**
     * Get config value.
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return the Url for create subscription.
     *
     * @return string
     */
    public function getCreateSubscriptionUrl()
    {
        return $this->_urlBuilder->getUrl('subscription/create/index');
    }
}
