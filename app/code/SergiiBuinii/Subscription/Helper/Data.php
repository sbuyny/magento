<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const GOOGLE_API_KEY = "SergiiBuinii_subscription/subscription_group/google_api_key";

    const ADDRESS_ERROR_MESSAGE = "SergiiBuinii_subscription/subscription_group/address_error_message";

    const US_ZIP_ERROR_MESSAGE = "SergiiBuinii_subscription/subscription_group/uszipcode_error_message";

    const CA_ZIP_ERROR_MESSAGE = "SergiiBuinii_subscription/subscription_group/cazipcode_error_message";

    const MILITARY_ERROR_MESSAGE = "APO/DPO/FPO Military cities should be assigned to AP, AE, or AA states only.";

    const ERROR_MESSAGE = "Unexpected Error Occurred. Please Try Again Later.";
    
    /**
     * Not logged in customer group code
     */
    const NOT_LOGGED_IN_GROUP_CODE = "NOT LOGGED IN";
    
    /**
     * Event for saved subscription
     */
    const SUBSCRIPTION_SAVE_SUCCESSFULLY_EVENT = 'subscription_save_successfully';
    
    /**
     * Event object data key for subscription
     */
    const EVENT_DATA_KYE_SUBSCRIPTION = 'subscription';

    /**
     * GoogleAPI key
     *
     * @return string
     */
    public function googleAPIKey()
    {
        return $this->scopeConfig->getValue(self::GOOGLE_API_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param StoreManagerInterface $storeManagerInterface
     * @param GroupRepositoryInterface $customerGroupRepository
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        StoreManagerInterface $storeManagerInterface,
        GroupRepositoryInterface $customerGroupRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->storeManagerInterface = $storeManagerInterface;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getCustomerGroup()
    {
        $groupId = $this->customerSession->getCustomer()->getGroupId();
        $customerGroupCode = $this->customerGroupRepository->getById($groupId);
        $finalCustomerGroupCode =  $customerGroupCode->getCode();
        $storeCode = $this->storeManagerInterface->getStore()->getCode();
        if(!$this->customerSession->isLoggedIn()){
            $codeArray = explode("_", $storeCode);
            switch($storeCode){
                Case in_array("ca", $codeArray):
                    $finalCustomerGroupCode = "CA Guest";
                    break;
                Case in_array("us", $codeArray):
                Default:
                    $finalCustomerGroupCode = "US Guest";
            }
        }
        return $finalCustomerGroupCode;
    }

    /**
     * @return string
     */
    public function getCustomerSubscriptionPointRegion()
    {
        $storeCode = $this->storeManagerInterface->getStore()->getCode();
            $codeArray = explode("_", $storeCode);
            switch($storeCode){
                Case in_array("ca", $codeArray):
                    $subpointRegion = "CA";
                    break;
                Case in_array("us", $codeArray):
                Default:
                $subpointRegion = "US";
            }
        return $subpointRegion;
    }

    /**
     * Address Error Message
     *
     * @return string
     */
    public function invalidErrorMessage()
    {
        return self::ERROR_MESSAGE;
    }

    /**
     * Address Error Message
     *
     * @return string
     */
    public function addressErrorMessage()
    {
        return $this->scopeConfig->getValue(self::ADDRESS_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Address Error Message
     *
     * @return string
     */
    public function usZipCodeErrorMessage()
    {
        return $this->scopeConfig->getValue(self::US_ZIP_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Address Error Message
     *
     * @return string
     */
    public function caZipCodeErrorMessage()
    {
        return $this->scopeConfig->getValue(self::CA_ZIP_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Military Error Message
     *
     * @return string
     */
    public function addressErrorMilitaryMessage()
    {
        return self::MILITARY_ERROR_MESSAGE;
    }

    /**
     * Regex Postal Code based on country
     *
     * @return array
     */
    public function postalCodeRegexArray()
    {
        $postalRegexChecker = [
            "US"=>"^\d{5}([\-]?\d{4})?$",
            "UK"=>"^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
            "DE"=>"\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
            "CA"=>"^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
            "FR"=>"^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
            "IT"=>"^(V-|I-)?[0-9]{5}$",
            "AU"=>"^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
            "NL"=>"^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
            "ES"=>"^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
            "DK"=>"^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
            "SE"=>"^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
            "BE"=>"^[1-9]{1}[0-9]{3}$"
        ];
        return $postalRegexChecker;
    }

    /**
     * Regex Address Contains PO Box Strings
     *
     * @return string
     */
    public function pOBoxRegexString()
    {
        $pOBoxRegexChecker = "/\s*((P(OST)?.?\s*(O(FF(ICE)?)?)?.?\s+(B(IN|OX))?)|B(IN|OX))/i";
        return $pOBoxRegexChecker;
    }

    /**
     * Regex String Military Cities APO/FPO/DPO
     * Army Post Office
     * Fleet Post Office
     * Diplomacy Post Office
     *
     * @return string
     */
    public function cityMilitaryRegexString()
    {
        $militaryBoxRegexChecker = "/\s*(ARMY)|(DIPLOMATIC)|(FLEET)|\s*(P(OST)?.?\s*(O(FF(ICE)?)?))/i";
        return $militaryBoxRegexChecker;
    }

}
