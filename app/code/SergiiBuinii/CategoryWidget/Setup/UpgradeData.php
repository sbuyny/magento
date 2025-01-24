<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CategoryWidget\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Model\Category\Attribute\Backend\Image;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use SergiiBuinii\CategoryWidget\Helper\Images;
use Magento\Framework\DB\Ddl\Table;
use SergiiBuinii\CategoryWidget\Model\Category\CategoryNav;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface $installer
     */
    protected $installer;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeSchema constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades DB schema for a module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer = $setup;
        $this->installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCategoryImageAttribute();
        } elseif (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addCategoryNavAttribute();
        }

        $this->installer->endSetup();
    }

    /**
     *  Create category attribute
     */
    protected function addCategoryImageAttribute()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->installer]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            Images::MOBILE_CATEGORY_IMAGE_ATTRIBUTE,
            [
                'type' => 'varchar',
                'label' => 'Mobile Image',
                'input' => 'image',
                'backend' => Image::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        )->addAttribute(
            Category::ENTITY,
            Images::TABLET_CATEGORY_IMAGE_ATTRIBUTE,
            [
                'type' => 'varchar',
                'label' => 'Tablet Image',
                'input' => 'image',
                'backend' => Image::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
    }

    /**
     * Created category attribute for top nav menu
     */
    protected function addCategoryNavAttribute()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->installer]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            CategoryNav::CATEGORY_NAV_NAME_ATTRIBUTE,
            [
                'type' => 'varchar',
                'label' => 'Dropdown Nav Label',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
    }
}
