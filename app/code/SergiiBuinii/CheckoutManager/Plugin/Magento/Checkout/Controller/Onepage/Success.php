<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Plugin\Magento\Checkout\Controller\Onepage;

use SergiiBuinii\CheckoutManager\Helper\Config;
use SergiiBuinii\CheckoutManager\Logger\Logger;
use Magento\Checkout\Controller\Onepage\Success as CheckoutControllerOnepageSuccess;
use Magento\Framework\App\Action\Context;
use Magento\Framework\HTTP\Header;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\View\Result\PageFactory;

/**
 * Plugin Class Success
 */
class Success
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \SergiiBuinii\CheckoutManager\Logger\Logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messagesManager;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var Config
     */
    private $helper;
    
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    private $request;
    
    /**
     * @var \Magento\Framework\HTTP\Header
     */
    private $header;
    
    /**
     * Success constructor.
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helper
     * @param \SergiiBuinii\CheckoutManager\Logger\Logger $logger
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @param \Magento\Framework\HTTP\Header $header
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context,
        Config $helper,
        Logger $logger,
        Request $request,
        Header $header
    ) {
        $this->objectManager = $context->getObjectManager();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messagesManager = $context->getMessageManager();
        $this->eventManager = $context->getEventManager();
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->request = $request;
        $this->header = $header;
    }

    /**
     * Around Plugin for order success execute
     *
     * @param CheckoutControllerOnepageSuccess $subject
     * @param callable $proceed
     * @return
     */
    public function aroundExecute(CheckoutControllerOnepageSuccess $subject, callable $proceed)
    {
        /** @var \Magento\Checkout\Model\Session $session */
        $session = $subject->getOnepage()->getCheckout();
        if (!$this->objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
            $sessionDataArr = $this->getCheckoutSessionData($session);
            // check and ensure order is created in admin before redirecting to order success page
            if (!$this->helper->isEnabled()) {
                $result = $this->resultRedirectFactory->create()->setPath('checkout/cart');

            } elseif (isset($sessionDataArr['order_number']) && isset($sessionDataArr['order_ids'])) {
                $this->logger->force('Forcing success page for... ', $sessionDataArr);
                $session->clearQuote();

                $result = $this->resultPageFactory->create();
                $this->eventManager->dispatch(
                    'checkout_onepage_controller_success_action',
                    [
                        'order_ids' => [$session->getLastOrderId()],
                        'order' => $session->getLastRealOrder()
                    ]
                );
            } else {
                $result = $this->resultRedirectFactory->create()->setPath('checkout/cart');
                $this->logger->fail(
                    'Checkout session was not valid...',
                    [
                        'clientIp'  => $this->request->getClientIp(),
                        'sessionId' => $session->getSessionId(),
                        'httpUserAgent' => $this->header->getHttpUserAgent()
                    ]
                );
                
                $this->messagesManager->addErrorMessage(
                    'Order was not placed. Please contact customer support.'
                );
            }
        } else {
            $lastOrderNumber = $session->getLastRealOrder()->getIncrementId();
            $this->logger->success('Using success page default behavior for... ' . $lastOrderNumber);
            $result = $proceed();
        }

        return $result;
    }

    /**
     * Get Checkout Session Data if available
     *
     * @param \Magento\Checkout\Model\Session $session
     * @return array
     */
    public function getCheckoutSessionData($session)
    {
        $data = [];

        if ($session->getLastRealOrder()) {
            $data = [
                'clientIp' => $this->request->getClientIp(),
                'sessionId' => $session->getSessionId(),
                'cookieDomain' => $session->getCookieDomain(),
                'cookiePath' => $session->getCookiePath(),
                'cookieLifetime' => $session->getCookieLifetime(),
                'order_ids' => [$session->getLastOrderId()],
                'order_number' => $session->getLastRealOrder()->getIncrementId()
            ];
        }
        $this->logger->info(print_r($data, true));
        return $data;
    }
}
