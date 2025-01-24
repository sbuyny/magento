<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */
namespace SergiiBuinii\CustomerDonation\Plugin\Magento\Quote\Model\Quote\Item;

use SergiiBuinii\CustomerDonation\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Processor as OriginalClass;

class Processor
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \SergiiBuinii\CustomerDonation\Helper\Config
     */
    private $config;

    /**
     * Processor constructor
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $session
     * @param \SergiiBuinii\CustomerDonation\Helper\Config $config
     */
    public function __construct(
        Http $request,
        Session $session,
        Config $config
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Add custom price to request object
     *
     * @param \Magento\Quote\Model\Quote\Item\Processor $subject
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param \Magento\Framework\DataObject $request
     * @param \Magento\Catalog\Model\Product $candidate
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePrepare(OriginalClass $subject, Item $item, DataObject $request, Product $candidate)
    {
        $params = $this->request->getParams();
        if (isset($params['is_donation'])) {
            if (empty($request->getCustomPrice())) {
                if (isset($params['custom_price'])) {
                    if ($params['custom_price'] < $this->config->getMinimumPrice()) {
                        $this->session->setIsInvalidDonationPrice(true);
                        throw new LocalizedException(
                            __('You can\'t add donation with %1 price.', $params['custom_price'])
                        );
                    }
                    $request->setCustomPrice($params['custom_price']);
                } else {
                    $request->setCustomPrice($this->config->getMinimumPrice());
                }
            }
        }
        return [$item, $request, $candidate];
    }
}
