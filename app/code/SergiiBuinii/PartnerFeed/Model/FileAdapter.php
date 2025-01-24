<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use SergiiBuinii\PartnerFeed\Logger\DebugLogger;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Class FileAdapter
 */
class FileAdapter
{
    /**#@+
     * Default values
     */
    const DEFAULT_MAIN_PATH = 'feed/';
    const DEFAULT_PROCESSED = 'feed/processed/';
    const DEFAULT_FAILED    = 'feed/failed/';
    const DEFAULT_EXT       = '.xml';
    const DEFAULT_HISTORY   = 'feed/history/';
    /**#@-  */
    
    /**
     * @var string
     */
    protected $mainPath;

    /**
     * @var string
     */
    protected $processed;

    /**
     * @var string
     */
    protected $failed;

    /**
     * @var string
     */
    protected $ext;
    
    /**
     * @var string
     */
    protected $history;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \SergiiBuinii\PartnerFeed\Logger\DebugLogger
     */
    protected $logger;
    
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directoryHandle;

    /**
     * FileAdapter constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \SergiiBuinii\PartnerFeed\Logger\DebugLogger $logger
     * @param array $data
     */
    public function __construct(
        Filesystem $filesystem,
        DebugLogger $logger,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->directoryHandle = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR, DriverPool::FILE);
        $this->initFolderStructure($data);
    }
    
    /**
     * Set main path
     *
     * @param string $path
     * @return $this
     */
    public function setMainPath($path)
    {
        $this->mainPath = $path;
        return $this;
    }
    
    /**
     * Set processed path
     *
     * @param string $path
     * @return $this
     */
    public function setProcessed($path)
    {
        $this->processed = $path;
        return $this;
    }
    
    /**
     * Set failed path
     *
     * @param string $path
     * @return $this
     */
    public function setFailed($path)
    {
        $this->failed = $path;
        return $this;
    }
    
    /**
     * Set ext path
     *
     * @param string $ext
     * @return $this
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
        return $this;
    }
    
    /**
     * Set history path
     *
     * @param string $path
     * @return $this
     */
    public function setHistory($path)
    {
        $this->history = $path;
        return $this;
    }
    
    /**
     * Set main path
     *
     * @param array $data
     * @return $this
     */
    protected function initFolderStructure($data)
    {
        $this->mainPath = $data['mainPath'] ?? self::DEFAULT_MAIN_PATH;
        $this->processed = $data['processed'] ?? self::DEFAULT_PROCESSED;
        $this->failed = $data['failed'] ?? self::DEFAULT_FAILED;
        $this->ext = $data['ext'] ?? self::DEFAULT_EXT;
        $this->history = $data['history'] ?? self::DEFAULT_HISTORY;
        return $this;
    }
    
    /**
     * Copy file file
     *
     * @param string $source
     * @param string $destination
     * @param bool $delete
     * @return bool
     */
    public function copyFile($source, $destination, $delete = false)
    {
        try {
            $this->directoryHandle->copyFile($source, $destination);
            if ($delete) {
                $this->directoryHandle->delete($source);
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return false;
    }

    /**
     * Move imported file
     *
     * @param string $source
     * @param bool $delete
     * @return bool
     */
    public function copyFileToProcessed($source, $delete = false)
    {
        return $this->copyFile($source, $this->getDestination($source), $delete);
    }

    /**
     * Move imported file
     *
     * @param string $source
     * @param bool $delete
     * @return bool
     */
    public function copyFileToFailed($source, $delete = false)
    {
        return $this->copyFile($source, $this->getFailed($source), $delete);
    }

    /**
     * Write file
     *
     * @param string $fileName
     * @param string $output
     * @return bool
     */
    public function writeFile($fileName, $output)
    {
        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface $writer */
        $writer = $this->directoryHandle;

        /** @var \Magento\Framework\Filesystem\File\WriteInterface $file */
        $file = $writer->openFile($fileName, 'w');

        try {
            $file->lock();
            try {
                $file->write($output);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                return false;
            } finally {
                $file->unlock();
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        } finally {
            $file->close();
        }

        return true;
    }

    /**
     * Retrieve destination path
     *
     * @param string $source
     * @return string
     */
    protected function getDestination($source)
    {
        return $this->processed . str_replace($this->mainPath, '', $source);
    }

    /**
     * Retrieve failed path
     *
     * @param string $source
     * @return string
     */
    protected function getFailed($source)
    {
        return $this->failed . str_replace($this->mainPath, '', $source);
    }
    
    /**
     * List import directory files
     *
     * @param string $relPath
     * @return array[]
     */
    public function listDirectory($relPath = '')
    {
        $path = $relPath ? $this->mainPath . $relPath : $this->mainPath;
        $files = $this->directoryHandle->search('*' . $this->ext, $path);
        
        return $files;
    }
    
    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->mainPath;
    }
    
    /**
     * Ger relative path
     *
     * @param string $name
     * @return string
     */
    public function getRelativePath($name)
    {
        return $this->mainPath . $name;
    }
    
    /**
     * Write the file
     *
     * @param string $destination
     * @param string $output
     * @param bool $addToHistory
     * @return bool
     */
    public function write($destination, $output, $addToHistory = false)
    {
        $fileName = $this->mainPath . $destination;
        
        if ($this->writeFile($fileName, $output)) {
            if ($addToHistory) {
                $this->addToHistory($fileName, $destination);
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Add imported file to history dir
     *
     * @param string $fileName
     * @param string $destination
     * @return bool
     */
    protected function addToHistory($fileName, $destination)
    {
        return $this->directoryHandle->copyFile($fileName, $this->history . $destination);
    }
    
    /**
     * Set local source directory
     *
     * @param string $path
     * @return bool
     */
    public function isExist($path)
    {
        if (!$this->directoryHandle->isExist($path)) {
            return false;
        }
        return true;
    }
    
    /**
     * Check is file
     *
     * @param string $fileRelativePath
     * @return bool
     */
    public function ifFile($fileRelativePath)
    {
        return $this->directoryHandle->isFile($fileRelativePath);
    }
    
    /**
     * Retrieve file list
     *
     * @param string $sourceDir
     * @return array|false
     */
    public function read($sourceDir)
    {
        try {
            return $this->directoryHandle->read($sourceDir);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }
    
    /**
     * Read file
     *
     * @param string $fileRelativePath
     * @return bool|string
     */
    public function readFile($fileRelativePath)
    {
        try {
            return $this->directoryHandle->readFile($fileRelativePath);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }
    
    /**
     * Retrieve file name from relative path
     *
     * @param string $fileRelativePath
     * @return string
     */
    public function getNameFromPath($fileRelativePath)
    {
        return str_replace($this->mainPath, '', $this->directoryHandle->getAbsolutePath($fileRelativePath));
    }
}
