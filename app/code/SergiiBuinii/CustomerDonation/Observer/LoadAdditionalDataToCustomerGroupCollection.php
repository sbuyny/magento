<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SergiiBuinii\CustomerDonation\Model\DonationProcessor;
use Magento\Customer\Model\ResourceModel\Group\Collection;

class LoadAdditionalDataToCustomerGroupCollection implements ObserverInterface
{
    /**
     * @var \SergiiBuinii\CustomerDonation\Model\DonationProcessor
     */
    protected $donationProcessor;

    /**
     * LoadAdditionalDataToCustomerGroupCollection constructor
     *
     * @param \SergiiBuinii\CustomerDonation\Model\DonationProcessor $donationProcessor
     */
    public function __construct(DonationProcessor $donationProcessor)
    {
        $this->donationProcessor = $donationProcessor;
    }

    /**
     * Load additional data to customer group collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection instanceof Collection) {
            foreach ($collection as $customerGroup) {
                $additionalData = $this->donationProcessor->getDonationData($customerGroup);
                if (!empty($additionalData)) {
                    $customerGroup->addData($additionalData);
                }
            }
        }
    }
}
