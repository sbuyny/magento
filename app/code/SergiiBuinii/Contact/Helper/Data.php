<?php

namespace SergiiBuinii\Contact\Helper;

use Magento\Contact\Model\ConfigInterface;

/**
 * Contact helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Get user first name
     *
     * @return string
     */
    public function getFirstName()
    {
        if (!$this->customerSession->isLoggedIn() && $this->customerSession->getCustomer()) {
            return '';
        }

        return $this->customerSession->getCustomer()->getFirstname();
    }

    /**
     * Get user last name
     *
     * @return string
     */
    public function getLastName()
    {
        if (!$this->customerSession->isLoggedIn() && $this->customerSession->getCustomer()) {
            return '';
        }

        return $this->customerSession->getCustomer()->getLastname();
    }
}
