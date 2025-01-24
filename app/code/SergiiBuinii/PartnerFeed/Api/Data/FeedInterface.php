<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Api\Data;

interface FeedInterface
{
    /**#@+
     * Data keys
     */
    const ENTITY_ID = 'entity_id';
    const PARTNER_ID = 'partner_id';
    const STATUS = 'status';
    const UPDATED_AT = 'updated_at';
    const AVAILABLE = 'available';
    const DATA = 'serialized_data';
    const ACTUAL_PRICE = 'actual_price';
    const UPC = 'upc';
    const SKU = 'sku';
    /**#@-  */

    /**
     * Get feed data array
     *
     * @return array|null
     */
    public function getDecodedData();

    /**
     * Set Serialized data
     *
     * @param string $data
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setSerializedData($data);

    /**
     * Get Feed Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Feed id
     *
     * @param int $id
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setId($id);

    /**
     * Get Partner Id
     *
     * @return int|null
     */
    public function getPartnerId();

    /**
     * Set Partner Id
     *
     * @param int $partnerId
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setPartnerId($partnerId);

    /**
     * Get Actual Price
     *
     * @return float|null
     */
    public function getActualPrice();

    /**
     * Set Actual price
     *
     * @param float $value
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setActualPrice($value);

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set Product Sku
     *
     * @param string $value
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setSku($value);
}
