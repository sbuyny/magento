<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Donation extends AbstractDb
{
    /**
     * Customer group donation table
     */
    const DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION = 'SergiiBuinii_customer_group_donation';

    /**#@+
     * Constants defined for table columns
     */
    const DB_SCHEMA_FIELD_DONATION_ID           = 'id';
    const DB_SCHEMA_FIELD_DONATION_STATUS       = 'status';
    const DB_SCHEMA_FIELD_DONATION_PRODUCTS     = 'products';
    const DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID     = 'customer_group_id';
    const DB_SCHEMA_FIELD_DONATION_REQUIRED       = 'donation_required';
    /**#@-*/

    /**#@+
     * Constants defined for data keys
     */
    const CUSTOMER_GROUP_DONATION_STATUS        = 'donation_status';
    const CUSTOMER_GROUP_DONATION_PRODUCT_IDS   = 'donation_product_ids';
    const CUSTOMER_GROUP_DONATION_REQUIRED      = 'donation_required';
    /**#@-*/

    const PRODUCT_IDS_SEPARATOR = ',';

    /**
     * @var array $schemaKeyMapping
     */
    protected $schemaKeyMapping = [
        self::DB_SCHEMA_FIELD_DONATION_STATUS   => self::CUSTOMER_GROUP_DONATION_STATUS,
        self::DB_SCHEMA_FIELD_DONATION_PRODUCTS => self::CUSTOMER_GROUP_DONATION_PRODUCT_IDS,
        self::DB_SCHEMA_FIELD_DONATION_REQUIRED   => self::CUSTOMER_GROUP_DONATION_REQUIRED,
    ];

    // @codingStandardsIgnoreStart
    /**
     * Define main table
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init(self::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION, self::DB_SCHEMA_FIELD_DONATION_ID);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Retrieve WHERE statement for DB adapter
     *
     * @param string $field
     * @return string
     */
    protected function bindWherePattern($field)
    {
        return sprintf('%s = :%s', $field, $field);
    }

    /**
     * Map data for model output
     *
     * @param array $tableData
     * @return array
     */
    protected function mapToModel($tableData)
    {
        $result = [];
        foreach ($tableData as $key => $value) {
            $result[$this->schemaKeyMapping[$key]] = $value;
        }
        return $result;
    }

    /**
     * Retrieve customer group donation data
     *
     * @param int|string $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroupDonation($customerGroupId)
    {
        $connection = $this->getConnection();
        $bind = [self::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID => (int) $customerGroupId];
        $select = $connection->select()->from(
            $this->getMainTable(),
            [self::DB_SCHEMA_FIELD_DONATION_STATUS, self::DB_SCHEMA_FIELD_DONATION_PRODUCTS, self::DB_SCHEMA_FIELD_DONATION_REQUIRED]
        )->where($this->bindWherePattern(self::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID));
        $tableData = $connection->fetchRow($select, $bind);

        return empty($tableData) ? [] : $this->mapToModel($tableData);
    }

    /**
     * Save donation data to DB
     *
     * @param int|string $customerGroupId
     * @param array $donationData
     * @return int 1|0
     */
    public function saveDonationData($customerGroupId, $donationData)
    {
        $result = 0;
        if (!empty($donationData)) {
            $connection = $this->getConnection();
            $insertData = [];
            $insertData[self::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID] = $customerGroupId;
            if (isset($donationData[self::CUSTOMER_GROUP_DONATION_STATUS])) {
                $insertData[self::DB_SCHEMA_FIELD_DONATION_STATUS] = (int) $donationData[self::CUSTOMER_GROUP_DONATION_STATUS];
            }
            if (isset($donationData[self::CUSTOMER_GROUP_DONATION_PRODUCT_IDS])) {
                $insertData[self::DB_SCHEMA_FIELD_DONATION_PRODUCTS] = $donationData[self::CUSTOMER_GROUP_DONATION_PRODUCT_IDS];
            }
            if (isset($donationData[self::CUSTOMER_GROUP_DONATION_REQUIRED])) {
                $insertData[self::DB_SCHEMA_FIELD_DONATION_REQUIRED] = (int) $donationData[self::CUSTOMER_GROUP_DONATION_REQUIRED];
            }
            $result = $connection->insertOnDuplicate(
                self::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION,
                $insertData,
                [self::DB_SCHEMA_FIELD_DONATION_STATUS, self::DB_SCHEMA_FIELD_DONATION_PRODUCTS, self::DB_SCHEMA_FIELD_DONATION_REQUIRED]
            );
        }
        return $result;
    }

    /**
     * Delete donation data from DB
     *
     * @param int|string $customerGroupId
     * @return int 1|0
     */
    public function deleteCustomerGroupDonation($customerGroupId)
    {
        $connection = $this->getConnection();
        return $connection->delete(
            self::DB_SCHEMA_TABLE_CUSTOMER_GROUP_DONATION,
            [self::DB_SCHEMA_FIELD_CUSTOMER_GROUP_ID . ' = ?' => (int) $customerGroupId]
        );
    }
}
