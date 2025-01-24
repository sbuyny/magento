<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Filesystem\Io;

use Magento\Framework\Filesystem\Io\Ftp as OriginFtp;

/**
 * Class Ftp
 */
class Ftp extends OriginFtp
{
    /**
     * Returns all errors
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->_conn->getErrors();
    }
    
    /**
     * Returns the last error
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->_conn->getLastError();
    }
}
