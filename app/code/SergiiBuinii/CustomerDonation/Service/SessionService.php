<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation as DonationResource;

class SessionService
{
    /**
     * NOT LOGGED IN customer group ID
     *
     * @type int
     */
    const NOT_LOGGED_IN_GROUP_ID = 0;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation
     */
    protected $donationResource;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * SessionService constructor
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation $donationResource
     */
    public function __construct(
        CustomerSession $customerSession,
        DonationResource $donationResource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->customerSession = $customerSession;
        $this->donationResource = $donationResource;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Check if donation widget available
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasDonation()
    {
        $donationData = $this->getDonationData();
        return !empty($donationData) && (int) $donationData[DonationResource::CUSTOMER_GROUP_DONATION_STATUS];
    }

    /**
     * Retrieve donation products
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDonationItems()
    {
        $products = [];
        $donationData = $this->getDonationData();

        if (!count($donationData) || !isset($donationData[DonationResource::CUSTOMER_GROUP_DONATION_PRODUCT_IDS])) {
            return $products;
        }

        $prodIds = explode(
            DonationResource::PRODUCT_IDS_SEPARATOR,
            $donationData[DonationResource::CUSTOMER_GROUP_DONATION_PRODUCT_IDS]
        );

        foreach ($prodIds as $id) {
            if ($id) {
                $products[] = $this->productRepository->getById($id);
            }
        }

        return $products;
    }

    /**
     * Retrieve donation data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDonationData()
    {
        if ($this->customerSession->isLoggedIn()) {
            $currentGroupId = $this->customerSession->getCustomer()->getGroupId();

            return $this->donationResource->getCustomerGroupDonation($currentGroupId);
        }

        return $this->donationResource->getCustomerGroupDonation(self::NOT_LOGGED_IN_GROUP_ID);
    }
}
