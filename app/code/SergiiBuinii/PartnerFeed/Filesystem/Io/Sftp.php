<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Filesystem\Io;

use Magento\Framework\Filesystem\Io\Sftp as OriginSftp;

/**
 * Class Sftp
 */
class Sftp extends OriginSftp
{
    /**
     * Returns all errors
     *
     * @return array
     */
    public function getSFTPErrors()
    {
        return $this->_connection->getSFTPErrors();
    }
    
    /**
     * Returns the last error
     *
     * @return string
     */
    public function getLastSFTPError()
    {
        return $this->_connection->getLastSFTPError();
    }
    
    /**
     * Returns all errors
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->_connection->getErrors();
    }
    
    /**
     * Returns the last error
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->_connection->getLastError();
    }
}
