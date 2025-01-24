<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface
     */
    private $installer;

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer = $setup;
        $this->installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addColumnForHttpSettings();
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addSkuColumnForFeed();
        }

        $this->installer->endSetup();
    }

    /**
     * Add columns in Partner table
     *
     * Add columns 'http_local_filename' and 'http_url'
     *
     * @return void
     */
    private function addColumnForHttpSettings()
    {
        $this->installer->getConnection()->addColumn(
            Partner::DB_SCHEMA_MAIN_TABLE,
            PartnerInterface::HTTP_LOCAL_FILENAME,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'HTTP Local Filename',
            ]
        );

        $this->installer->getConnection()->addColumn(
            Partner::DB_SCHEMA_MAIN_TABLE,
            PartnerInterface::HTTP_URL,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'HTTP Url',
            ]
        );
    }

    /**
     * Add columns in Feed table
     *
     * Add columns 'sku'
     *
     * @return void
     */
    private function addSkuColumnForFeed()
    {
        $this->installer->getConnection()->addColumn(
            Feed::DB_SCHEMA_MAIN_TABLE,
            FeedInterface::SKU,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Product Sku',
            ]
        );
    }
}
