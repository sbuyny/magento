<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Plugin\Magento\Quote\Model\Quote;

use Magento\Framework\App\Request\Http;
use Magento\Quote\Model\Quote\Item as OriginalClass;

class Item
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Quote Item plugin constructor
     *
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    /**
     * Additional check product representation in item
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @see \Magento\Quote\Model\Quote\Item::representProduct()
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param bool $result
     * @return bool
     */
    public function afterRepresentProduct(OriginalClass $subject, $result)
    {
        $params = $this->request->getParams();
        if ($result && isset($params['is_donation']) && isset($params['custom_price'])) {
            return $subject->getCustomPrice() == $params['custom_price'];
        }
        return $result;
    }
}
