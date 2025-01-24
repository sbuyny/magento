<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use SergiiBuinii\Subscription\Api\Data\SubscriptionInterface;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $tableName = $installer->getTable(Subscription::DB_SCHEMA_TABLE_SUBSCRIPTION);

        if ($installer->getConnection()->isTableExists($tableName)) {
            return;
        }

        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                SubscriptionInterface::SUBSCRIPTION_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                SubscriptionInterface::EMAIL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )->addColumn(
                SubscriptionInterface::FIRST_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'First Name'
            )->addColumn(
                SubscriptionInterface::LAST_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Last Name'
            )->addColumn(
                SubscriptionInterface::STREET,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Street'
            )->addColumn(
                SubscriptionInterface::ADDITIONAL_STREET,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Additional Street'
            )->addColumn(
                SubscriptionInterface::POSTAL_CODE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Postal code'
            )->addColumn(
                SubscriptionInterface::CITY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'City'
            )->addColumn(
                SubscriptionInterface::REGION,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Region'
            )->addColumn(
                SubscriptionInterface::COUNTRY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Country'
            );

        $installer->getConnection()->createTable($table);
        
        $installer->endSetup();
    }
}
