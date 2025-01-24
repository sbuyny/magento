<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Plugin\Magento\Checkout\Model;

use SergiiBuinii\CheckoutManager\Helper\Config;
use SergiiBuinii\CheckoutManager\Logger\Logger;
use Magento\Checkout\Model\PaymentInformationManagement as OriginalClass;
use Magento\Customer\Model\Session;
use Magento\Framework\HTTP\Header;
use Magento\Framework\HTTP\PhpEnvironment\Request;

/**
 * Class PaymentInformationManagement
 */
class PaymentInformationManagement
{
    /**
     * @var \SergiiBuinii\CheckoutManager\Helper\Config
     */
    private $helperConfig;

    /**
     * @var \SergiiBuinii\CheckoutManager\Logger\Logger
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    private $request;
    
    /**
     * @var \Magento\Framework\HTTP\Header
     */
    private $header;
    
    /**
     * PaymentInformationManagement constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helperConfig
     * @param \SergiiBuinii\CheckoutManager\Logger\Logger $logger
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
      * @param \Magento\Framework\HTTP\Header $header

     */
    public function __construct(
        Session $customerSession,
        Config $helperConfig,
        Logger $logger,
        Request $request,
        Header $header

    ) {
        $this->logger = $logger;
        $this->helperConfig = $helperConfig;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->header = $header;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param callable $proceed
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int Order ID.
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        OriginalClass $subject,
        callable $proceed,
        string $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($this->helperConfig->isEnabled()) {
            $this->logger->info('PAYMENT-INFORMATION');
            $paymentSessionData = ['cartId'=>$cartId];
            if ($billingAddress !== null) {
                $paymentSessionData += ['name'=> $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname()];
            }

            $paymentSessionData += ['payment method'=>$paymentMethod->getMethod()];
            $paymentSessionData += ['clientIp' => $this->request->getClientIp()];
            $paymentSessionData += ['httpUserAgent' => $this->header->getHttpUserAgent()];
            $this->logger->info(print_r($paymentSessionData, true));
        }

        return $proceed($cartId, $paymentMethod, $billingAddress);
    }
}
