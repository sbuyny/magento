<?php

namespace SergiiBuinii\Vip\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends AbstractHelper
{
    /**#@+
     * System config xml constant variables
     * @type string
     */
    const VIP_GROUP = 'vip_group';
    const CUSTOMER_GROUP = 'customer_group';
    /**#@- */
    
    /**
     *  Xml path to check_expiration_date config
     */
    const CHECK_EXPIRATION_DATE = 'SergiiBuinii_vip_customer/general/check_expiration_date';
    
    /**
     *  Xml path to downgrade_mapping config
     */
    const DOWNGRADE_MAPPING = 'SergiiBuinii_vip_customer/general/downgrade_mapping';
    
    /**
     * @var int[]
     */
    protected $groupMapping;
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;
    
    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        Json $serializer
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
    }
    
    /**
     * Is expiration date must be checked
     *
     * @return bool
     */
    public function checkExpirationDate()
    {
        return $this->scopeConfig->isSetFlag(self::CHECK_EXPIRATION_DATE);
    }
    
    /**
     * Get Downgrade mapping
     *
     * Return array of mapping of customer groups
     * [
     *    vip_group => customer_group,
     *    ...
     * ]
     *
     * @return int[]
     */
    public function getDowngradeMapping()
    {
        if (null !== $this->groupMapping) {
            return $this->groupMapping;
        }
        $value = $this->scopeConfig->getValue(self::DOWNGRADE_MAPPING);
        return $this->groupMapping = $value ?
            $this->serializer->unserialize($value)
            : [];
    }
}
