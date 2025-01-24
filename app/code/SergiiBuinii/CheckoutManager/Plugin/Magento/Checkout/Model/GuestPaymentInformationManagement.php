<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Plugin\Magento\Checkout\Model;

use SergiiBuinii\CheckoutManager\Helper\Config;
use SergiiBuinii\CheckoutManager\Logger\Logger;
use Magento\Checkout\Model\GuestPaymentInformationManagement as OriginalClass;
use Magento\Framework\HTTP\Header;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class GuestPaymentInformationManagement
 */
class GuestPaymentInformationManagement
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
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    private $request;
    
    /**
     * @var \Magento\Framework\HTTP\Header
     */
    private $header;
    
    /**
     * GuestPaymentInformationManagement constructor.
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helperConfig
     * @param \SergiiBuinii\CheckoutManager\Logger\Logger $logger
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @param \Magento\Framework\HTTP\Header $header
     */
    public function __construct(
        Config $helperConfig,
        Logger $logger,
        Request $request,
        Header $header
    ) {
        $this->logger = $logger;
        $this->helperConfig = $helperConfig;
        $this->request = $request;
        $this->header = $header;
    }

    /**
     * Around plugin for savePaymentInformationAndPlaceOrder
     *
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @param callable $proceed
     * @param string $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int Order ID.
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        OriginalClass $subject,
        callable $proceed,
        string $cartId,
        string $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($this->helperConfig->isEnabled()) {
            $this->logger->info('GUEST PAYMENT-INFORMATION');
            $paymentSessionData = ['cartId'=>$cartId];

            if ($billingAddress !== null) {
                $paymentSessionData += ['name'=> $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname()];
            }
            $paymentSessionData += ['email'=>$email];
            $paymentSessionData += ['clientIp' => $this->request->getClientIp()];
            $paymentSessionData += ['payment method'=>$paymentMethod->getMethod()];
            $paymentSessionData += ['httpUserAgent' => $this->header->getHttpUserAgent()];
            $this->logger->info(print_r($paymentSessionData, true));
        }

        return $proceed($cartId, $email, $paymentMethod, $billingAddress);
    }
}
