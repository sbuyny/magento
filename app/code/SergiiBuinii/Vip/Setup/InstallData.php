<?php

namespace SergiiBuinii\Vip\Setup;

use SergiiBuinii\Vip\Helper\Data;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime as BackendDatetime;
use Magento\Eav\Model\Entity\Attribute\Frontend\Datetime as FrontendDatetime;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $setFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    private $resourceAttr;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    private $customerSetupFactory;

    /**
     * InstallData constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Customer\Model\ResourceModel\Attribute $resourceAttr
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $setFactory,
        Attribute $resourceAttr
    ) {
        $this->setFactory = $setFactory;
        $this->resourceAttr = $resourceAttr;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        try {
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        } catch (LocalizedException $e) {
            $customerEntity = null;
        }

        $attributeSetId = $customerEntity ? $customerEntity->getDefaultAttributeSetId() : null;

        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $this->setFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            Data::VIP_CUSTOMER_ATTR_EXP_DATE,
            [
                'type'          => 'datetime',
                'label'         => 'VIP Expiration Date',
                'input'         => 'date',
                'frontend'      => FrontendDatetime::class,
                'backend'       => BackendDatetime::class,
                'user_defined'  => true,
                'system'        => false,
                'required'      => false,
                'visible'       => false,
                'sort_order'    => 90,
                'position'      => 90,
            ]
        );

        try {
            $vipExpDateAttr = $customerSetup->getEavConfig()->getAttribute(
                Customer::ENTITY,
                Data::VIP_CUSTOMER_ATTR_EXP_DATE
            );
        } catch (LocalizedException $e) {
            $vipExpDateAttr = null;
        }

        if ($vipExpDateAttr) {
            $vipExpDateAttr->addData([
                'attribute_set_id'      => $attributeSetId,
                'attribute_group_id'    => $attributeGroupId,
                'used_in_forms'         => ['adminhtml_customer'],
            ]);

            try {
                $this->resourceAttr->save($vipExpDateAttr);
            } catch (AlreadyExistsException $e) {
                //do nothing
            } catch (\Exception $e) {
                //do nothing
            }
        }
    }
}
