<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Api\Data;

/**
 * Interface PartnerInterface
 */
interface PartnerInterface
{
    /**#@+
     * Data keys
     */
    const ENTITY_ID = 'entity_id';
    const STATUS = 'status';
    const CONNECTION_TYPE = 'connection_type';
    const NAME = 'name';
    const FTP_USER = 'ftp_user';
    const FTP_PASSWORD = 'ftp_password';
    const FTP_HOST = 'ftp_host';
    const FTP_REMOTE_FOLDER = 'ftp_remote_folder';
    const FTP_REMOTE_FILENAME = 'ftp_remote_filename';
    const FTP_LOCAL_FILENAME = 'ftp_local_filename';
    const HTTP_LOCAL_FILENAME = 'http_local_filename';
    const HTTP_URL = 'http_url';
    /**#@-  */

    /**
     * Get Partner Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Partner id
     *
     * @param int $id
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setId($id);

    /**
     * Get Partner Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Partner Id
     *
     * @param int $status
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setStatus($status);

    /**
     * Get Partner Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set Partner Name
     *
     * @param string $name
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setName($name);

    /**
     * Get Connection Type
     *
     * @return int|null
     */
    public function getConnectionType();

    /**
     * Set Connection Type
     *
     * @param int $type
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setConnectionType($type);

    /**
     * Get Connection Parameters
     *
     * @return array
     */
    public function getConnectionParameters();
}
