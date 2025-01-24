<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Setup;

use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * InstallSchema class
 *
 * Creates new table 'SergiiBuinii_customer_group_donation'
 * for additional data storage
 *
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->createCustomerGroupDonationTable($installer);
        $installer->endSetup();
    }

    /**
     * Create table 'SergiiBuinii_customer_group_donation' for storing additional data for customer group(s)
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createCustomerGroupDonationTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Donation::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION)
        )->addColumn(
            Donation::DB_SCHEMA_FIELD_DONATION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Donation Info ID'
        )->addColumn(
            Donation::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group ID'
        )->addColumn(
            Donation::DB_SCHEMA_FIELD_DONATION_STATUS,
            Table::TYPE_BOOLEAN,
            null,
            [],
            'Customer Group Donation Status'
        )->addColumn(
            Donation::DB_SCHEMA_FIELD_DONATION_PRODUCTS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Group Donation Products'
        )->addIndex(
            $installer->getIdxName(
                Donation::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION,
                [Donation::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [Donation::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                Donation::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION,
                Donation::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID,
                'customer_group',
                'customer_group_id'
            ),
            Donation::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID,
            $installer->getTable('customer_group'),
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Customer Group Donation Table'
        );
        $installer->getConnection()->createTable($table);
    }
}
