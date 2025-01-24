<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use SergiiBuinii\PartnerFeed\Helper\Config as ConfigHelper;
use SergiiBuinii\PartnerFeed\Filesystem\Io\SftpFactory;

/**
 * Class SftpAdapter
 */
class SftpAdapter
{
    const TYPE_FILE = 1;
    const TYPE_DIR  = 2;

    /**
     * @var \SergiiBuinii\PartnerFeed\Filesystem\Io\Sftp
     */
    protected $connection;

    /**
     * @var \SergiiBuinii\PartnerFeed\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \SergiiBuinii\PartnerFeed\Filesystem\Io\SftpFactory
     */
    protected $adapterFactory;
    
    /**
     * @var string
     */
    protected $sourceDir;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * SftpAdapter constructor.
     * @param \SergiiBuinii\PartnerFeed\Helper\Config $configHelper
     * @param \SergiiBuinii\PartnerFeed\Filesystem\Io\SftpFactory $adapterFactory
     */
    public function __construct(
        ConfigHelper $configHelper,
        SftpFactory $adapterFactory
    ) {
        $this->configHelper = $configHelper;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * Connect to an SFTP server
     *
     * @return $this
     */
    public function connect()
    {
        if (null !== $this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
        $this->connection = $this->createConnection();

        return $this;
    }

    /**
     * Retrieve connection
     *
     * @return \SergiiBuinii\PartnerFeed\Filesystem\Io\Sftp
     */
    protected function getConnection()
    {
        if (null === $this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * Get credentials for connection
     *
     * @return array
     * @throws \Exception
     */
    protected function getCredentials()
    {
        if ($this->settings) {
            return $this->settings;
        }
    }

    /**
     * Create connection to an SFTP server using specified configuration
     *
     * @return \SergiiBuinii\PartnerFeed\Filesystem\Io\Sftp
     * @throws \Exception
     */
    public function createConnection()
    {
        $config = $this->getCredentials();
    
        /** @var \SergiiBuinii\PartnerFeed\Filesystem\Io\Sftp $connection */
        $connection = $this->adapterFactory->create();
        try {
            $connection->open(
                [
                    'host' => $config['host'],
                    'username' => $config['username'],
                    'password' => $config['password']
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception(
                __('Connection error: %1;', $e->getMessage())
                .PHP_EOL.
                __('Credentials: %1', print_r($config, true))
            );
        }

        return $connection;
    }
    
    /**
     * Change directory
     *
     * @param string $path
     * @return $this
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function cd($path)
    {
        if (!$this->getConnection()->cd($path)) {
            throw new \Exception(__(
                'Directory "%1" does not exist on server.',
                $path
            ));
        };
        
        return $this;
    }

    /**
     * Retrieve current directory in connection
     *
     * @return string
     */
    public function getCurrentDir()
    {
        return $this->getConnection()->pwd();
    }
    
    /**
     * Fetch all files from current directory on server
     *
     * @param string|null $pattern
     * @return array
     */
    public function fetchAll($pattern = null)
    {
        $list = $this->getFilteredFileList($pattern);
        $result = [];

        foreach (array_keys($list) as $name) {
            $content = $this->fetchFile($name);
            if ($content) {
                $result[$name] = $content;
            }
        }

        return $result;
    }

    /**
     * Fetch file content from server
     *
     * @param string $name
     *
     * @return string
     */
    public function fetchFile($name)
    {
        return trim($this->getConnection()->read($name));
    }
    
    /**
     * Get file list from server, filtered by pattern
     *
     * @param string|null $pattern
     * @return array
     */
    public function getFilteredFileList($pattern = null)
    {
        $list = [];
        $pattern = $pattern ?? '/[.]+/';

        foreach ($this->getConnection()->rawls() as $name => $data) {
            if ($data['type'] == static::TYPE_FILE && preg_match($pattern, $name)) {
                $list[$name] = $data;
            }
        }

        return $list;
    }
    
    /**
     * Upload file
     *
     * @param string $filename
     * @param string $content
     * @return $this
     * @throws \Exception
     */
    public function upload($filename, $content)
    {
        if (!$this->getConnection()->write($filename, $content)) {
            throw new \Exception(__(
                'Can not upload file "%1" to "%2". Error: %3',
                $filename,
                $this->getConnection()->pwd(),
                $this->getConnection()->getLastSFTPError()
            ));
        };
        
        return $this;
    }
    
    /**
     * Rename file
     *
     * @param string $source
     * @param string $destination
     * @return $this
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function mv($source, $destination)
    {
        $this->getConnection()->rm($destination);
        if (!$this->getConnection()->mv($source, $destination)) {
            throw new \Exception(__(
                'Can rename file from file "%1" to "%2". Error: %3',
                $source,
                $destination,
                $this->getConnection()->getLastSFTPError()
            ));
        };
    
        return $this;
    }

    /**
     * Set Credentials
     *
     * @param array $options
     */
    public function setCredentials($options)
    {
        $this->settings = array_merge($this->settings, $options);
    }
}
