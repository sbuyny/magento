<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Setup;

use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface
     */
    private $installer;

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer = $setup;
        $this->installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addDonationRequiredField();
        }

        $this->installer->endSetup();
    }

    /**
     * Add donation_required column
     */
    private function addDonationRequiredField()
    {
        $table = $this->installer->getTable(Donation::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION);

        $donationRequiredColumnName = Donation::DB_SCHEMA_FIELD_DONATION_REQUIRED;
    
        if ($this->installer->getConnection()->tableColumnExists($table, $donationRequiredColumnName) === false) {
            $this->installer->getConnection()->addColumn(
                $table,
                $donationRequiredColumnName,
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'length' => null,
                    'default' => '0',
                    'comment' => 'Is donation required'
                ]
            );
        }
    }
}
