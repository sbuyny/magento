<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Service\File\Download;

use SergiiBuinii\PartnerFeed\Model\Debugger;
use SergiiBuinii\PartnerFeed\Model\FtpAdapter;
use SergiiBuinii\PartnerFeed\Model\SftpAdapter;
use SergiiBuinii\PartnerFeed\Model\HttpAdapter;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;

/**
 * Class Service
 */
class Service
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Helper\Config
     */
    protected $config;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\Debugger
     */
    protected $debugger;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\SftpAdapter
     */
    protected $sftpAdapter;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FtpAdapter
     */
    protected $ftpAdapter;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\HttpAdapter
     */
    protected $httpAdapter;

    /**
     * @var \SergiiBuinii\PartnerFeed\Service\File\ParseService
     */
    protected $parseService;

    /**
     * Service constructor
     *
     * @param \SergiiBuinii\PartnerFeed\Model\Debugger $debugger
     * @param \SergiiBuinii\PartnerFeed\Model\SftpAdapter $sftpAdapter
     * @param \SergiiBuinii\PartnerFeed\Model\FtpAdapter $ftpAdapter
     * @param \SergiiBuinii\PartnerFeed\Model\HttpAdapter $httpAdapter
     */
    public function __construct(
        Debugger $debugger,
        SftpAdapter $sftpAdapter,
        FtpAdapter $ftpAdapter,
        HttpAdapter $httpAdapter
    ) {
        $this->sftpAdapter = $sftpAdapter;
        $this->debugger = $debugger;
        $this->ftpAdapter = $ftpAdapter;
        $this->httpAdapter = $httpAdapter;
    }

    /**
     * Execute service
     *
     * @param array $data
     * @return string
     */
    public function execute($data)
    {
        $type = $data[PartnerInterface::CONNECTION_TYPE];
        switch ($type) {
            case 0:
                return $this->httpDownload($data);
                break;
            case 1:
                return $this->ftpDownload($data);
                break;
            case 2:
                return $this->sftpDownload($data);
                break;
        }
    }

    /**
     * Download via HTTP Adaptor
     *
     * @param array $data
     * @return string
     *
     * @SuppressWarnings("unused")
     */
    private function httpDownload($data)
    {
        return $this->httpAdapter->getFile($data['http_url']);
    }

    /**
     * Download via FTP Adaptor
     *
     * @param array $data
     * @return string
     *
     * @SuppressWarnings("unused")
     */
    private function ftpDownload($data)
    {
        $this->ftpAdapter->setCredentials(
            [
                'host' => $data['ftp_host'],
                'username' => $data['ftp_user'],
                'password' => $data['ftp_password'],
                'ssl' => true,
                'passive' => true
            ]
        );
        $this->ftpAdapter->connect()->cd($data['ftp_remote_folder']);
        return $this->ftpAdapter->fetchFile($data['ftp_remote_filename']);
    }

    /**
     * Download via SFTP Adaptor
     *
     * @param array $data
     * @return string
     */
    private function sftpDownload($data)
    {
        $this->sftpAdapter->setCredentials(
            [
                'host' => $data['ftp_host'],
                'username' => $data['ftp_user'],
                'password' => $data['ftp_password'],
            ]
        );
        $this->sftpAdapter->connect()->cd($data['ftp_remote_folder']);
        return $this->sftpAdapter->fetchFile($data['ftp_remote_filename']);
    }
}
