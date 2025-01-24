<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Api\Data;

interface SubscriptionInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const SUBSCRIPTION_ID       = 'subscription_id';
    const EMAIL                 = 'email';
    const FIRST_NAME            = 'firstname';
    const LAST_NAME             = 'lastname';
    const STREET                = 'street';
    const ADDITIONAL_STREET     = 'additional_street';
    const POSTAL_CODE           = 'postcode';
    const CITY                  = 'city';
    const REGION                = 'region';
    const COUNTRY               = 'country_id';
    const CUSTOMER_GROUP        = 'customer_group_id';
    const SUBPOINT_REGION_ID    = 'subpoint_region_id';
    const SOURCE_BANNER_ID      = 'source_banner_id';
    const SOURCE_FOOTER_ID      = 'source_footer_id';
    const SOURCE_CATALOG_ID     = 'source_catalog_id';
    /**#@-*/

    /**
     * Retrieve subscription ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Retrieve email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Retrieve first name
     *
     * @return string|null
     */
    public function getFirstName();

    /**
     * Retrieve last name
     *
     * @return string|null
     */
    public function getLastName();

    /**
     * Retrieve street
     *
     * @return string|null
     */
    public function getStreet();

    /**
     * Retrieve additional street
     *
     * @return string|null
     */
    public function getAdditionalStreet();

    /**
     * Retrieve postal Code
     *
     * @return string|null
     */
    public function getPostCode();

    /**
     * Retrieve city
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Retrieve region
     *
     * @return string|int|null
     */
    public function getRegion();

    /**
     * Retrieve country
     *
     * @return string|null
     */
    public function getCountry();

    /**
     * Retrieve customer group
     *
     * @return string|null
     */
    public function getCustomerGroup();

    /**
     * Retrieve subscription point region
     *
     * @return string|null
     */
    public function getSubpointRegion();

    /**
     * Retrieve source banner value
     *
     * @return int
     */
    public function getSourceBanner();

    /**
     * Retrieve source footer value
     *
     * @return int
     */
    public function getSourceFooter();

    /**
     * Retrieve source catalog value
     *
     * @return int
     */
    public function getSourceCatalog();

    /**
     * Set subscription ID
     *
     * @param int $id
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setId($id);

    /**
     * Set email
     *
     * @param string $email
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setEmail($email);

    /**
     * Set first name
     *
     * @param string $firstName
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setFirstName($firstName);

    /**
     * Set last name
     *
     * @param string $lastName
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setLastName($lastName);

    /**
     * Set street
     *
     * @param string $street
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setStreet($street);

    /**
     * Set additional street
     *
     * @param string $additionalStreet
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setAdditionalStreet($additionalStreet);

    /**
     * Set postal code
     *
     * @param string $postCode
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setPostCode($postCode);

    /**
     * Set city
     *
     * @param string $city
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setCity($city);

    /**
     * Set region
     *
     * @param int|string $region
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setRegion($region);

    /**
     * Set country
     *
     * @param string $country
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setCountry($country);

    /**
     * Set customer group
     *
     * @param string $customerGroup
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setCustomerGroup($customerGroup);

    /**
     * Set subscription point region
     *
     * @param string $subpointRegion
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setSubpointRegion($subpointRegion);

    /**
     * Set source banner
     *
     * @param boolean $sourceBanner
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setSourceBanner($sourceBanner);

    /**
     * Set source footer
     *
     * @param boolean $sourceFooter
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setSourceFooter($sourceFooter);

    /**
     * Set source catalog
     *
     * @param boolean $sourceCatalog
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     */
    public function setSourceCatalog($sourceCatalog);
}
