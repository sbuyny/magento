<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Model;

use Magento\Customer\Model\Group;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation;

class DonationProcessor
{
    /**
     * @var \SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation
     */
    protected $donationResource;

    /**
     * DonationProcessor constructor
     *
     * @param \SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation $donationResource
     */
    public function __construct(Donation $donationResource)
    {
        $this->donationResource = $donationResource;
    }

    /**
     * Get donation data from customer group model
     *
     * @param $customerGroup
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDonationData($customerGroup)
    {
        return $this->donationResource->getCustomerGroupDonation($customerGroup->getId());
    }

    /**
     * Save donation data from customer group model
     *
     * @param \Magento\Customer\Model\Group $customerGroup
     * @return $this
     */
    public function saveDonationData(Group $customerGroup)
    {
        $this->donationResource->saveDonationData(
            $customerGroup->getId(),
            [
                Donation::CUSTOMER_GROUP_DONATION_STATUS => $customerGroup->getData(Donation::CUSTOMER_GROUP_DONATION_STATUS),
                Donation::CUSTOMER_GROUP_DONATION_PRODUCT_IDS => $customerGroup->getData(Donation::CUSTOMER_GROUP_DONATION_PRODUCT_IDS),
                Donation::CUSTOMER_GROUP_DONATION_REQUIRED => $customerGroup->getData(Donation::CUSTOMER_GROUP_DONATION_REQUIRED),
            ]
        );
        return $this;
    }
}
