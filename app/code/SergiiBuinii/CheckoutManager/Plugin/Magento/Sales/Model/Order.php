<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Plugin\Magento\Sales\Model;

use SergiiBuinii\CheckoutManager\Helper\Config;
use SergiiBuinii\CheckoutManager\Logger\Logger;
use Magento\Customer\Model\Session;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Sales\Model\Order as SalesModelOrder;

/**
 * Plugin Class Order
 */
class Order
{
    /**
     * @var \SergiiBuinii\CheckoutManager\Logger\Logger
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \SergiiBuinii\CheckoutManager\Helper\Config
     */
    private $helperConfig;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * Order constructor.
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helperConfig
     * @param \SergiiBuinii\CheckoutManager\Logger\Logger $logger
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        Session $customerSession,
        Config $helperConfig,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->helperConfig = $helperConfig;
        $this->customerSession = $customerSession;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Around plugin for place()
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param callable $proceed
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    public function aroundPlace(
        SalesModelOrder $subject,
        callable $proceed
    ) {
        $data = $this->getCustomerSessionData($subject);
        if ($this->helperConfig->isEnabled() && count($data)>0) {
            $this->logger->info(print_r($this->getCustomerSessionData($subject), true));
        }

        return $proceed();
    }

    /**
     * Get Customer Session Data
     * @param \Magento\Sales\Model\Order $subject
     * @return array
     */
    public function getCustomerSessionData($subject)
    {
        $data = ['Ip'=> $this->remoteAddress->getRemoteAddress()];

        if ($subject->getId()) {
            $data += ['QuoteId' => $subject->getQuoteId()];
        }
        if ($this->customerSession->getId()) {
            $data += [
                'sessionId' => $this->customerSession->getSessionId(),
                'cookieDomain' => $this->customerSession->getCookieDomain(),
                'cookiePath' => $this->customerSession->getCookiePath(),
                'cookieLifetime' => $this->customerSession->getCookieLifetime()
            ];
        } else {
            $data += ['customerSession'=>'none'];
        }

        return $data;
    }
}
