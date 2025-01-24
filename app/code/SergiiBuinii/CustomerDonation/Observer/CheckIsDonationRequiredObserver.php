<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SergiiBuinii\CustomerDonation\Model\DonationProcessor;
use SergiiBuinii\CustomerDonation\Service\SessionService;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation;
use Magento\Framework\Message\ManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class CheckIsDonationRequiredObserver implements ObserverInterface
{
    /**
     * @var \SergiiBuinii\CustomerDonation\Model\DonationProcessor
     */
    protected $donationProcessor;

    /**
     * @var \SergiiBuinii\CustomerDonation\Service\SessionService
     */
    protected $sessionService;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\ResourceModel\GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * CheckIsDonationRequiredObserver constructor.
     * @param \SergiiBuinii\CustomerDonation\Model\DonationProcessor $donationProcessor
     * @param \SergiiBuinii\CustomerDonation\Service\SessionService $sessionService
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\ResourceModel\GroupRepository $groupRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        DonationProcessor $donationProcessor,
        SessionService $sessionService,
        Session $customerSession,
        GroupRepository $groupRepository,
        ManagerInterface $messageManager,
        CheckoutSession $checkoutSession
    ) {
        $this->donationProcessor = $donationProcessor;
        $this->sessionService = $sessionService;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Load additional data to customer group
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Model\Group $customerGroup */
        $customerGroup = $this->groupRepository->getById($this->customerSession->getCustomerGroupId());
        $donationData = $this->donationProcessor->getDonationData($customerGroup);

        if ($this->sessionService->hasDonation() &&
            $donationData[Donation::CUSTOMER_GROUP_DONATION_REQUIRED] &&
            !$this->hasDonation($donationData)
        ) {
            $this->messageManager->addErrorMessage(__('Please proceed to contribute to a charity of your choosing'));
            $observer->getControllerAction()->getResponse()->setRedirect('cart');
            return;
        }
    }

    /**
     * Check has donation in cart
     *
     * @param array $donationProducts
     */
    private function hasDonation($donationData)
    {
        if (isset($donationData[Donation::CUSTOMER_GROUP_DONATION_PRODUCT_IDS])) {
            $prodIds = explode(
                Donation::PRODUCT_IDS_SEPARATOR,
                $donationData[Donation::CUSTOMER_GROUP_DONATION_PRODUCT_IDS]
            );
            $quote = $this->checkoutSession->getQuote();

            foreach ($quote->getAllVisibleItems() as $cartItem) {
                if (in_array($cartItem->getProduct()->getId(), $prodIds)) {
                    return true;
                }
            }
        }

        return false;
    }
}
