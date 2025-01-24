<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SergiiBuinii\CustomerDonation\Model\DonationProcessor;

class SaveAdditionalDataToCustomerGroup implements ObserverInterface
{
    /**
     * @var \SergiiBuinii\CustomerDonation\Model\DonationProcessor
     */
    protected $donationProcessor;

    /**
     * SaveAdditionalDataToCustomerGroup constructor
     *
     * @param \SergiiBuinii\CustomerDonation\Model\DonationProcessor $donationProcessor
     */
    public function __construct(DonationProcessor $donationProcessor)
    {
        $this->donationProcessor = $donationProcessor;
    }

    /**
     * Save additional data to customer group
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Model\Group $customerGroup */
        $customerGroup = $observer->getEvent()->getDataObject();
        $this->donationProcessor->saveDonationData($customerGroup);
    }
}
