<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use SergiiBuinii\PartnerFeed\Helper\Config as ConfigHelper;
use SergiiBuinii\PartnerFeed\Filesystem\Driver\Http;

/**
 * Class HttpAdapter
 */
class HttpAdapter
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \SergiiBuinii\PartnerFeed\Filesystem\Driver\Http
     */
    protected $http;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * HttpAdapter constructor
     *
     * @param \SergiiBuinii\PartnerFeed\Helper\Config $configHelper
     * @param \SergiiBuinii\PartnerFeed\Filesystem\Driver\Http $http
     */
    public function __construct(
        ConfigHelper $configHelper,
        Http $http
    ) {
        $this->configHelper = $configHelper;
        $this->http = $http;
    }

    /**
     * Get file content
     *
     * @param string $path
     * @return string
     *
     * @throws \Exception
     */
    public function getFile($path)
    {
        if ($this->http->isExists($path)) {
            return $this->http->fileGetContents($path);
        }
        throw new \Exception('Requested file is not exists');
    }
}
