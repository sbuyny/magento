<?php

namespace SergiiBuinii\Contact\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /**#@+
     * System config fields
     *
     * @type string
     */
    const XML_PATH_BA_CONTACT_TOPIC = 'SergiiBuinii_contact/general/topic';

    const XML_PATH_BA_CONTACT_REDIRECT_URL = 'SergiiBuinii_contact/general/redirect_url';
    /**#@- */

    /**
     * Retrieve serialize contact type topics
     *
     * @return string
     */
    public function getTopic()
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_BA_CONTACT_TOPIC);
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_BA_CONTACT_REDIRECT_URL);
    }
}
