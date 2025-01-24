<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SergiiBuinii\CustomerDonation\Model\DonationProcessor;

class LoadAdditionalDataToCustomerGroup implements ObserverInterface
{
    /**
     * @var \SergiiBuinii\CustomerDonation\Model\DonationProcessor
     */
    protected $donationProcessor;

    /**
     * LoadAdditionalDataToCustomerGroup constructor
     *
     * @param \SergiiBuinii\CustomerDonation\Model\DonationProcessor $donationProcessor
     */
    public function __construct(DonationProcessor $donationProcessor)
    {
        $this->donationProcessor = $donationProcessor;
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
        $customerGroup = $observer->getEvent()->getDataObject();
        $donationData = $this->donationProcessor->getDonationData($customerGroup);
        $customerGroup->addData($donationData);
    }
}
