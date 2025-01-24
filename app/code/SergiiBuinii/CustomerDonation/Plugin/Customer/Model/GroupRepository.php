<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Plugin\Customer\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\ResourceModel\GroupRepository as OriginalClass;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation as DonationResource;

class GroupRepository
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation
     */
    private $donationResource;

    /**
     * @var array $serializedParams
     */
    private $serializedParams = [
        DonationResource::CUSTOMER_GROUP_DONATION_PRODUCT_IDS,
    ];

    /**
     * @var array $observableParams
     */
    private $observableParams = [
        DonationResource::CUSTOMER_GROUP_DONATION_STATUS,
        DonationResource::CUSTOMER_GROUP_DONATION_PRODUCT_IDS,
        DonationResource::CUSTOMER_GROUP_DONATION_REQUIRED,
    ];

    /**
     * GroupRepository constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation $donationResource
     */
    public function __construct(RequestInterface $request, DonationResource $donationResource)
    {
        $this->request = $request;
        $this->donationResource = $donationResource;
    }

    /**
     * Save donation data for customer group
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @see \Magento\Customer\Model\ResourceModel\GroupRepository::save()
     *
     * @param \Magento\Customer\Model\ResourceModel\GroupRepository $subject
     * @param \Magento\Customer\Api\Data\GroupInterface $result
     *
     * @return \Magento\Customer\Api\Data\GroupInterface $result
     */
    public function afterSave(OriginalClass $subject, $result)
    {
        if (!empty($donationData = $this->getDonationData())) {
            $this->donationResource->saveDonationData($result->getId(), $donationData);
        }

        return $result;
    }

    /**
     * Retrieve donation data from $_POST
     *
     * @return array
     */
    private function getDonationData()
    {
        $data = [];

        foreach ($this->observableParams as $observableParam) {
            $value = $this->request->getParam($observableParam);
            if ($value || $value == '0') {
                if (in_array($observableParam, $this->serializedParams)) {
                    $value = implode(
                        DonationResource::PRODUCT_IDS_SEPARATOR,
                        array_keys(json_decode($value, true))
                    );
                }
                $data[$observableParam] = $value;
            }
        }

        return $data;
    }
}
