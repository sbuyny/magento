<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CheckoutManager Helper Config
 */
class Config extends AbstractHelper
{
    /**
     * Is module enabled config path
     */
    const XML_PATH_ENABLED = 'SergiiBuinii_checkoutmanager/general/enabled';

    /**
     * debug config path
     */
    const XML_PATH_DEBUG_ENABLED = 'SergiiBuinii_checkoutmanager/general/debug_mode';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
    }

    /**
     * Check if module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Check if Debug Mode is Enabled
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return (bool) $this->getConfig(self::XML_PATH_DEBUG_ENABLED);
    }


    /**
     * Get configuration setting
     *
     * @param string $path
     * @param string $scopeType
     * @param int|null $store
     * @return string|bool
     */
    public function getConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $store = null)
    {
        if ($store === null) {
            try {
                $store = $this->storeManager->getStore()->getId();
            } catch (\Exception $e) {
                return false;
            }
        }

        return $this->scopeConfig->getValue(
            $path,
            $scopeType,
            $store
        );
    }

}
