<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Model;

use Magento\Framework\Model\AbstractModel;
use SergiiBuinii\Subscription\Api\Data\SubscriptionInterface;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription as ResourceModel;

class Subscription extends AbstractModel implements SubscriptionInterface
{
    // @codingStandardsIgnoreStart
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
    // @codingStandardsIgnoreEnd

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::SUBSCRIPTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalStreet()
    {
        return $this->getData(self::ADDITIONAL_STREET);
    }

    /**
     * @inheritdoc
     */
    public function getPostCode()
    {
        return $this->getData(self::POSTAL_CODE);
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritdoc
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroup()
    {
        return $this->getData(self::CUSTOMER_GROUP);
    }

    /**
     * @inheritdoc
     */
    public function getSubpointRegion()
    {
        return $this->getData(self::SUBPOINT_REGION_ID);
    }

    /**
     * @inheritdoc
     */
    public function getSourceBanner()
    {
        return $this->getData(self::SOURCE_BANNER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getSourceFooter()
    {
        return $this->getData(self::SOURCE_FOOTER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getSourceCatalog()
    {
        return $this->getData(self::SOURCE_CATALOG_ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::SUBSCRIPTION_ID, (int) $id);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    /**
     * @inheritdoc
     */
    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    /**
     * @inheritdoc
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritdoc
     */
    public function setAdditionalStreet($additionalStreet)
    {
        return $this->setData(self::ADDITIONAL_STREET, $additionalStreet);
    }

    /**
     * @inheritdoc
     */
    public function setPostCode($postCode)
    {
        return $this->setData(self::POSTAL_CODE, $postCode);
    }

    /**
     * @inheritdoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritdoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritdoc
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroup($customerGroup)
    {
        return $this->setData(self::CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * @inheritdoc
     */
    public function setSubpointRegion($subpointRegion)
    {
        return $this->setData(self::SUBPOINT_REGION_ID, $subpointRegion);
    }

    /**
     * @inheritdoc
     */
    public function setSourceBanner($sourceBanner)
    {
        return $this->setData(self::SOURCE_BANNER_ID, $sourceBanner);
    }

    /**
     * @inheritdoc
     */
    public function setSourceFooter($sourceFooter)
    {
        return $this->setData(self::SOURCE_FOOTER_ID, $sourceFooter);
    }

    /**
     * @inheritdoc
     */
    public function setSourceCatalog($sourceCatalog)
    {
        return $this->setData(self::SOURCE_CATALOG_ID, $sourceCatalog);
    }
}
