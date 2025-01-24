<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Observer;

use SergiiBuinii\CheckoutManager\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\OrderRepository;

/**
 * Class AddCheckoutSuccessMessage Observer
 */
class AddCheckoutAfterPlaceOrderStatusMessage implements ObserverInterface
{
    /** @var \Magento\Framework\Message\ManagerInterface  */
    private $messagesManager;

    /** @var \Magento\Sales\Model\OrderRepository  */
    private $orderRepository;
    /**
     * @var Config
     */
    private $helperConfig;

    /**
     * AddCheckoutAfterPlaceOrderStatusMessage constructor.
     *
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helperConfig
     */
    public function __construct(
        ManagerInterface $messageManager,
        OrderRepository $orderRepository,
        Config $helperConfig
    ) {
        $this->messagesManager = $messageManager;
        $this->orderRepository = $orderRepository;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->helperConfig->isEnabled()) {
            $orderIds = $observer->getOrderIds();
            if (count($orderIds) > 0) {
                $orders = $this->getOrderNumbers($orderIds);
                $this->messagesManager->addSuccessMessage("Successfully Placed Order: " . $orders);
            } else {
                $this->messagesManager->addWarningMessage(
                    "Order was not placed. Please Try again later or contact customer support."
                );
            }
        }
    }

    /**
     * Get Magento Order Numbers
     *
     * @param array $orderIds
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOrderNumbers($orderIds)
    {
        $orderIncIds = [];
        foreach ($orderIds as $orderId) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderRepository->get($orderId);
            $orderIncIds[] = $order->getIncrementId();
        }

        return implode(",", $orderIncIds);
    }
}
