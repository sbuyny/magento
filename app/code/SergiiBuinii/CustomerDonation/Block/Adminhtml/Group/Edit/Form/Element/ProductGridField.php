<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Block\Adminhtml\Group\Edit\Form\Element;

use Magento\Backend\Block\Template;
use SergiiBuinii\CustomerDonation\Plugin\Customer\Block\Adminhtml\Group\Edit\Form;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation as DonationResource;

class ProductGridField extends Template
{
    // @codingStandardsIgnoreStart
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'customer/group/edit/products.phtml';
    // @codingStandardsIgnoreEnd

    /**
     * @var \SergiiBuinii\CustomerDonation\Block\Adminhtml\Group\Edit\Form\Element\Field\ProductGrid
     */
    protected $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                Field\ProductGrid::class,
                'group.edit.product.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * Retrieve input name
     *
     * @return string
     */
    public function getInputName()
    {
        return DonationResource::CUSTOMER_GROUP_DONATION_PRODUCT_IDS;
    }

    /**
     * Retrieve product grid element name/key
     *
     * @return string
     */
    public function getProductGridKey()
    {
        return Form::PRODUCT_GRID_FORM_KEY;
    }
}
