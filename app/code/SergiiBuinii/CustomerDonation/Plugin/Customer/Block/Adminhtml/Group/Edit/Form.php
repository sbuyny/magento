<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Plugin\Customer\Block\Adminhtml\Group\Edit;

use Magento\Framework\Registry;
use Magento\Customer\Model\GroupRegistry;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Customer\Block\Adminhtml\Group\Edit\Form as OriginalClass;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation;
use SergiiBuinii\CustomerDonation\Block\Adminhtml\Group\Edit\Form\ProductGridRenderer;

class Form
{
    /**
     * Product grid field key
     *
     * @type string
     */
    const PRODUCT_GRID_FORM_KEY = 'product_grid';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Customer\Model\GroupRegistry
     */
    private $groupRegistry;

    /**
     * Form constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\GroupRegistry $groupRegistry
     */
    public function __construct(
        Registry $registry,
        GroupRegistry $groupRegistry
    ) {
        $this->registry = $registry;
        $this->groupRegistry = $groupRegistry;
    }

    /**
     * Add additional fields to customer group form
     *
     * @param \Magento\Framework\Data\Form $form
     * @see \Magento\Customer\Block\Adminhtml\Group\Edit\Form::setForm()
     * @param \Magento\Customer\Block\Adminhtml\Group\Edit\Form $subject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return array
     */
    public function beforeSetForm(OriginalClass $subject, $form)
    {
        $groupId = $this->registry->registry(RegistryConstants::CURRENT_GROUP_ID);

        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addType(
            self::PRODUCT_GRID_FORM_KEY,
            ProductGridRenderer::class
        );

        $status = $fieldset->addField(
            Donation::CUSTOMER_GROUP_DONATION_STATUS,
            'select',
            [
                'name' => Donation::CUSTOMER_GROUP_DONATION_STATUS,
                'label' => __('Enable Donation Widget'),
                'title' => __('Enable Donation Widget'),
                'required' => true,
                'values' => [
                    0 => 'No',
                    1 => 'Yes'
                ]
            ]
        );

        $donationRequired = $fieldset->addField(
            Donation::CUSTOMER_GROUP_DONATION_REQUIRED,
            'select',
            [
                'name' => Donation::CUSTOMER_GROUP_DONATION_REQUIRED,
                'label' => __('Donation Required'),
                'title' => __('Donation Required'),
                'required' => true,
                'values' => [
                    0 => 'No',
                    1 => 'Yes'
                ]
            ]
        );

        $denyReason = $fieldset->addField(
            self::PRODUCT_GRID_FORM_KEY,
            self::PRODUCT_GRID_FORM_KEY,
            [
                'name' => self::PRODUCT_GRID_FORM_KEY,
            ]
        );

        $subject->setChild(
            'form_after',
            $subject->getLayout()->createBlock(Dependence::class)
            ->addFieldMap($status->getHtmlId(), $status->getName())
            ->addFieldMap($denyReason->getHtmlId(), $denyReason->getName())
            ->addFieldDependence(
                $denyReason->getName(),
                $status->getName(),
                1
            )
        );

        if ($groupId !== null) {
            $form
                ->getElement($status->getHtmlId())
                ->setValue($this->groupRegistry->retrieve($groupId)->getData(Donation::CUSTOMER_GROUP_DONATION_STATUS));
            $form
                ->getElement($donationRequired->getHtmlId())
                ->setValue($this->groupRegistry->retrieve($groupId)->getData(Donation::CUSTOMER_GROUP_DONATION_REQUIRED));
        }

        return [$form];
    }
}
