<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */
namespace SergiiBuinii\CartAbandonmentEmail\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoresConfig;

class Data extends AbstractHelper
{
    /**
     * System config for Cart Abandonment
     */
    const XPATH_CART_ABANDONMENT_ALLOW = 'SergiiBuinii_cartabandonmentemail/general_settings/enabled';
    const XPATH_CART_ABANDONMENT_BASKETID = 'SergiiBuinii_cartabandonmentemail/general_settings/basket_id';
    const XPATH_CART_ABANDONMENT_HTML = 'SergiiBuinii_cartabandonmentemail/general_settings/abandon_html';
    const XPATH_CART_ABANDONMENT_TEMPLATE_ID = 'SergiiBuinii_cartabandonmentemail/general_settings/template_id';

    const XPATH_CART_ABANDONMENT_CRON_ABANDON_LIFETIME = 'SergiiBuinii_cartabandonmentemail/cron_settings/email_send_lifetime';
    const XPATH_CART_ABANDONMENT_CRON_ABANDON_SCHEDULE = 'SergiiBuinii_cartabandonmentemail/cron_settings/email_send_schedule';

    const XPATH_CART_ABANDONMENT_CRON_ABANDON_FILTER = 'SergiiBuinii_cartabandonmentemail/filter_settings/email_filter';
    const XPATH_CART_ABANDONMENT_CRON_ABANDON_FILTER_ENABLED = 'SergiiBuinii_cartabandonmentemail/filter_settings/enabled';

    const XPATH_CHECKOUT_PRO_CUSTOMER_GROUP = 'SergiiBuinii_checkoutverbiage/shipping_method/pro_customer_group';


    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Registry
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var StoresConfig|StoresConfig
     */
    protected $storesConfig;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param StoresConfig $storesConfig
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Registry $registry,
        StoresConfig $storesConfig
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->storesConfig = $storesConfig;
    }

    /**
     * Perform check if allowed to send Abandoned Emails
     *
     * @return bool
     */
    public function isAllowAbandonmentEmails()
    {
        return $this->scopeConfig->isSetFlag(
            self::XPATH_CART_ABANDONMENT_ALLOW,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get abandoned email Basket Url segmentation id
     *
     * @return string
     */
    public function getSegmentationIdAbandonBasketId()
    {
        return $this->scopeConfig->getValue(
            self::XPATH_CART_ABANDONMENT_BASKETID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get abandoned email Abandoned Html segmentation id
     *
     * @return string
     */
    public function getSegmentationIdAbandonHtml()
    {
        return $this->scopeConfig->getValue(
            self::XPATH_CART_ABANDONMENT_HTML,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get abandoned email Template id
     *
     * @return string
     */
    public function getTemplateIdAbandonedCart()
    {
        return $this->scopeConfig->getValue(
            self::XPATH_CART_ABANDONMENT_TEMPLATE_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return a generated Html table of abandoned items list
     *
     * @param $abandonDatas array
     * @param $basketIdPath string
     * @return string
     */
    public function generateHtmlAbandonItemsList($abandonDatas, $basketIdPath)
    {
        $itemHtml = '';
        if (isset($abandonDatas)) {
            $itemHtml .= '<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation"><tbody>';
            foreach ($abandonDatas as $abandonData) {
                $itemHtml .= '<tr>';
                if (isset($abandonData['thumbnail']) && !empty($abandonData['thumbnail'])) {
                    $itemHtml .= '<th class="" align="center" width="25%" style="font-weight:normal;">';
                    $itemHtml .= '<a href="'. $basketIdPath .'" target="_blank" title="PRODUCT" style="white-space:nowrap">';
                    $itemHtml .= '<img class="center" src="'.$abandonData['thumbnail'].'" width="300" border="0" alt="Product"
                    style="font-family:Montserrat,Arial,Helvetica,sans-serif; font-size:20px; display:block">';
                    $itemHtml .= '</th>';
                }
                $itemHtml .= '</tr>';
                $itemHtml .= '<th class="pad-t-20" align="center" width="45%" style="font-weight:normal;">';
                $itemHtml .= '<tr>';
                $itemHtml .= '</a>';
                $itemHtml .= '<td class="body center" align="center" style="font-family:Arial, Helvetica, sans-serif;
                    font-size:23px; font-weight:700; text-transform:uppercase; width:370px; padding-top:20px;">';
                $itemHtml .= htmlentities($abandonData['name']);
                $itemHtml .= '</td>';
                $itemHtml .='</tr>';
                if(isset($abandonData["pro_price"]) && isset($abandonData["base_price"]) && !empty($abandonData["pro_price"])) {
                    $itemHtml .= '<tr>';
                    $itemHtml .= '<td class="body center" align="center" style="font-family:Arial, Helvetica, sans-serif;
                    font-size:18px; font-weight:700; text-transform:uppercase; width:370px; padding-top:20px; padding-bottom:50px">';
                    $itemHtml .= '<strike>' .$abandonData["base_price"] . '</strike>'. ' Your Price: '. $abandonData["pro_price"];
                    $itemHtml .= '</td>';
                    $itemHtml .= '</tr>';
                } else if(isset($abandonData["base_price"])){
                    $itemHtml .= '<tr>';
                    $itemHtml .= '<td class="body center" align="center" style="font-family:Arial, Helvetica, sans-serif;
                    font-size:18px; font-weight:700; text-transform:uppercase; width:370px; padding-top:20px; padding-bottom:50px">';
                    $itemHtml .= $abandonData["base_price"];
                    $itemHtml .= '</td>';
                    $itemHtml .= '</tr>';
                }
            }
            $itemHtml .= '</tbody></table>';
        }
        return $itemHtml;
    }

    /**
     * Retrieve Pro Group Ids
     *
     * @return array
     */
    public function getProGroupIds()
    {
        $groupIds = $this->scopeConfig->getValue(
            self::XPATH_CHECKOUT_PRO_CUSTOMER_GROUP,
            ScopeInterface::SCOPE_STORE
        );

        return explode(',', $groupIds);
    }

    /**
     * Get Cron Lifetime for Abandonment Emails
     *
     * @return array
     */
    public function getAbandonmentCronLifetime()
    {
        return $this->storesConfig->getStoresConfigByPath(
            self::XPATH_CART_ABANDONMENT_CRON_ABANDON_LIFETIME
        );
    }

    /**
     * Retrieve Scheduled days
     *
     * @return string
     */
    public function getScheduledDays()
    {
        return $this->scopeConfig->getValue(
            self::XPATH_CART_ABANDONMENT_CRON_ABANDON_SCHEDULE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Perform check if allowed to filter abandonment emails
     * @return bool
     */
    public function isAbandonmentFilterAllowed()
    {
        return $this->scopeConfig->isSetFlag(
            self::XPATH_CART_ABANDONMENT_CRON_ABANDON_FILTER_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve abandonment filters via system configuration
     *
     * @return array
     */
    public function getAbandonmentFilters()
    {
        $attributes = $this->scopeConfig->getValue(
            self::XPATH_CART_ABANDONMENT_CRON_ABANDON_FILTER,
            ScopeInterface::SCOPE_STORE
        );
        if (!isset($attributes) || !$attributes || empty($attributes)) {
            return [];
        }

        $attributes = $this->unserialize($attributes);

        $attributeAbandonment = [];
        if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                if (isset($attribute['abandonment_enabled']) && $attribute['abandonment_enabled'] == 1) {
                    $attributeAbandonment[] = [
                        'filter_name' => $attribute['abandonment_fieldname'],
                        'filter_option' => $attribute['abandonment_option'],
                        'filter_value' => $attribute['abandonment_fieldvalue']
                    ];
                }
            }
        }
        return $attributeAbandonment;
    }

    /**
     * Unserialize string
     *
     * @param $string
     * @return array
     */
    protected function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }
}
