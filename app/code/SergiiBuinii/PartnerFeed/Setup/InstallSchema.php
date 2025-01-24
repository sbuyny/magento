<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Setup;

use Magento\Framework\DB\Ddl\Table;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Run install process
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        $partnerTable = $this->getPartnerTable($setup);
        $setup->getConnection()->createTable($partnerTable);

        $feedTable = $this->getFeedTable($setup);
        $setup->getConnection()->createTable($feedTable);

        $setup->endSetup();
    }

    /**
     * Build flat table for Feed entity
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return \Magento\Framework\DB\Ddl\Table
     */
    private function getFeedTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Feed::DB_SCHEMA_MAIN_TABLE))
            ->addColumn(
                FeedInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Feed ID'
            )
            ->addColumn(
                FeedInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                ['default' => '0','nullable' => false],
                'Feed status'
            )
            ->addColumn(
                FeedInterface::PARTNER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned'=>true, 'nullable'=>false, 'default' => '0'],
                'Partner Id'
            )
            ->addColumn(
                FeedInterface::UPC,
                Table::TYPE_TEXT,
                255,
                [],
                'UPC Code'
            )
            ->addColumn(
                FeedInterface::ACTUAL_PRICE,
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Actual Price'
            )
            ->addColumn(
                FeedInterface::AVAILABLE,
                Table::TYPE_INTEGER,
                null,
                [],
                'Available Qty'
            )
            ->addColumn(
                FeedInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Updated at'
            )
            ->addColumn(
                FeedInterface::DATA,
                Table::TYPE_TEXT,
                null,
                [],
                'Feed data'
            )
            ->addIndex(
                $setup->getIdxName(
                    Feed::DB_SCHEMA_MAIN_TABLE,
                    [FeedInterface::PARTNER_ID, FeedInterface::UPC],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [FeedInterface::PARTNER_ID, FeedInterface::UPC],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    Feed::DB_SCHEMA_MAIN_TABLE,
                    FeedInterface::PARTNER_ID,
                    Partner::DB_SCHEMA_MAIN_TABLE,
                    PartnerInterface::ENTITY_ID
                ),
                FeedInterface::PARTNER_ID,
                $setup->getTable(Partner::DB_SCHEMA_MAIN_TABLE),
                PartnerInterface::ENTITY_ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Sergii Buinii Partner Feeds');

        return $table;
    }

    /**
     * Build flat table for Feed entity
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return \Magento\Framework\DB\Ddl\Table
     */
    private function getPartnerTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Partner::DB_SCHEMA_MAIN_TABLE))
            ->addColumn(
                PartnerInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Partner ID'
            )
            ->addColumn(
                PartnerInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                ['default' => '0','nullable' => false],
                'Partner status'
            )
            ->addColumn(
                PartnerInterface::CONNECTION_TYPE,
                Table::TYPE_BOOLEAN,
                null,
                ['default' => '0','nullable' => false],
                'COnnection Type'
            )
            ->addColumn(
                PartnerInterface::NAME,
                Table::TYPE_TEXT,
                255,
                [],
                'Partner Name'
            )
            ->addColumn(
                PartnerInterface::FTP_HOST,
                Table::TYPE_TEXT,
                255,
                [],
                'FTP Host Url'
            )
            ->addColumn(
                PartnerInterface::FTP_USER,
                Table::TYPE_TEXT,
                255,
                [],
                'FTP User name'
            )
            ->addColumn(
                PartnerInterface::FTP_PASSWORD,
                Table::TYPE_TEXT,
                255,
                [],
                'FTP User Password'
            )
            ->addColumn(
                PartnerInterface::FTP_REMOTE_FOLDER,
                Table::TYPE_TEXT,
                255,
                [],
                'FTP Remote Folder'
            )
            ->addColumn(
                PartnerInterface::FTP_REMOTE_FILENAME,
                Table::TYPE_TEXT,
                255,
                [],
                'FTP Remote Filename'
            )
            ->addColumn(
                PartnerInterface::FTP_LOCAL_FILENAME,
                Table::TYPE_TEXT,
                255,
                [],
                'FTP Local Filename'
            )
            ->setComment('Partner Entity');

        return $table;
    }
}
