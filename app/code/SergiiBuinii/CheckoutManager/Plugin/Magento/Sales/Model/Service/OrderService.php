<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Plugin\Magento\Sales\Model\Service;

use SergiiBuinii\CheckoutManager\Helper\Config;
use SergiiBuinii\CheckoutManager\Logger\Logger;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Service\OrderService as SalesOrderService;

/**
 * Plugin Class OrderService
 */
class OrderService
{
    /**
     * @var \SergiiBuinii\CheckoutManager\Logger\Logger
     */
    private $logger;

    /**
     * @var \SergiiBuinii\CheckoutManager\Helper\Config
     */
    private $helperConfig;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * OrderService constructor.
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helperConfig
     * @param \SergiiBuinii\CheckoutManager\Logger\Logger $logger
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        Config $helperConfig,
        Logger $logger
    ) {
        $this->helperConfig = $helperConfig;
        $this->remoteAddress = $remoteAddress;
        $this->logger = $logger;
    }

    /**
     * Around plugin for place()
     *
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param callable $proceed
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Exception
     */
    public function aroundPlace(
        SalesOrderService $subject,
        callable $proceed,
        OrderInterface $order
    ) {
        if (!$this->helperConfig->isEnabled()) {
            return $proceed($order);
        }
        $this->logger->info('BEGIN ORDER HANDLER ====');
        $data = ['Ip' => $this->remoteAddress->getRemoteAddress()];
        if ($order->getQuoteId()) {
            $data += [
                'quoteId' => $order->getQuoteId(),
                'name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                'email' => $order->getCustomerEmail()
            ];
        }
        try {
            /** @var \Magento\Sales\Model\Order $result */
            $result = $proceed($order);
            if (!$result->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Error occurred. Order was not created.')
                );
            }
            $data += ['OrderID: ' => $result->getIncrementId()];
            $this->logger->success(print_r($data, true));
            $this->logger->success(__FUNCTION__
                . ',['
                . $result->getIncrementId()
                . ' :: '
                . $result->getRemoteIp()
                . ']');
        } catch (\Exception $e) {
            $this->logger->fail(print_r($data, true));
            $this->logger->critical($e->getMessage());
            $result = $order;
            throw $e;
        } finally {
            $this->logger->info('END ORDER HANDLER ====');
        }

        return $result;
    }
}
