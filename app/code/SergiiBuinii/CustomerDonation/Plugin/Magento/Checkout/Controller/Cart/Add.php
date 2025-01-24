<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Plugin\Magento\Checkout\Controller\Cart;

use Magento\Checkout\Controller\Cart\Add as OriginalClass;
use Magento\Checkout\Model\Session;
use Magento\Framework\UrlInterface;

class Add
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * Add constructor
     *
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        Session $session,
        UrlInterface $url
    ) {
        $this->session = $session;
        $this->url = $url;
    }

    /**
     * Remove product URL from redirect URL if custom donation price is invalid
     *
     * @param \Magento\Checkout\Controller\Cart\Add $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(OriginalClass $subject, $result)
    {
        if ($this->session->getIsInvalidDonationPrice()) {
            $result->setUrl($this->url->getUrl('checkout/cart', ['_secure' => true]));
        }
        return $result;
    }
}
