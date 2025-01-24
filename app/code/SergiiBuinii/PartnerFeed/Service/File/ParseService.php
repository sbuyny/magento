<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Service\File;

use SergiiBuinii\PartnerFeed\Model\Debugger;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Class ParseService
 */
class ParseService
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directoryHandle;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\Debugger
     */
    private $debugger;

    /**
     * ParseService constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \SergiiBuinii\PartnerFeed\Model\Debugger $debugger
     */
    public function __construct(
        Filesystem $filesystem,
        Debugger $debugger
    ) {
        $this->directoryHandle = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR, DriverPool::FILE);
        $this->debugger = $debugger;
    }

    /**
     * Return Feeds
     *
     * @param string $data
     * @return \SimpleXMLElement
     */
    public function parseFileData($data)
    {
        $products = new \SimpleXMLElement($data);
        return $products;
    }
}
