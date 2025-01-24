<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Controller\Create;

use SergiiBuinii\Subscription\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\NotFoundException;
use SergiiBuinii\Subscription\Model\SubscriptionFactory;
use SergiiBuinii\Subscription\Model\SubscriptionRepository;
use SergiiBuinii\Subscription\Api\Data\SubscriptionInterface;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\Subscription\Helper\Data as SubscriptionHelper;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    /**
     * @var \SergiiBuinii\Subscription\Model\SubscriptionFactory $subscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @var \SergiiBuinii\Subscription\Model\SubscriptionRepository $subscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $json
     */
    protected $json;

    /**
     * @var \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    protected $resultFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var \SergiiBuinii\Subscription\Helper\Data $subscripHelper
     */
    protected $subscripHelper;

    /**
     * @var \Magento\Directory\Model\RegionFactory $regionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Index constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \SergiiBuinii\Subscription\Model\SubscriptionFactory $subscriptionFactory
     * @param \SergiiBuinii\Subscription\Model\SubscriptionRepository $subscriptionRepository
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \SergiiBuinii\Subscription\Helper\Data $subscripHelper
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curl
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SubscriptionFactory $subscriptionFactory,
        SubscriptionRepository $subscriptionRepository,
        Json $json,
        ResultFactory $resultFactory,
        SubscriptionHelper $subscripHelper,
        RegionFactory $regionFactory,
        CurlFactory $curl,
        JsonHelper $jsonHelper,
        LoggerInterface $logger
    ) {
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->json = $json;
        $this->resultFactory = $resultFactory;
        $this->subscripHelper = $subscripHelper;
        $this->regionFactory = $regionFactory;
        $this->jsonHelper = $jsonHelper;
        $this->curl = $curl;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Create subscription
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $response = ['success' => true];
        $data = $this->getRequest()->getParams();
        if (!isset($data['isAjax']) || !$data['isAjax']) {
            throw new NotFoundException(__('Router not found'));
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $model = $this->subscriptionFactory->create();
            $model->setData($data);

            if (isset($data[SubscriptionInterface::STREET][0]) && !empty($data[SubscriptionInterface::STREET][0])) {
                $model->setStreet($data[SubscriptionInterface::STREET][0]);
            }

            $postalCode = $model->getPostCode();
            $countryCode = $model->getCountry();
            $region = $model->getRegion();
            $city = $model->getCity();
            $streetAddress = $model->getStreet();

            if (isset($data[SubscriptionInterface::STREET][1]) && !empty($data[SubscriptionInterface::STREET][1])) {
                $model->setAdditionalStreet($data[SubscriptionInterface::STREET][1]);
            }

            if (!$region && isset($data['region_id'])) {
                $model->setRegion($data['region_id']);
            }

            if ($countryCode) {
                if (!preg_match("/".$this->subscripHelper->postalCodeRegexArray()[$countryCode]."/i",$postalCode)) {
                    if ($countryCode == "US") {
                        $response = [
                            'success' => false,
                            'message' => $this->subscripHelper->usZipCodeErrorMessage()
                        ];
                        return $resultJson->setData($response);
                    } else {
                        $response = [
                            'success' => false,
                            'message' => $this->subscripHelper->caZipCodeErrorMessage()
                        ];
                        return $resultJson->setData($response);
                    }

                }
            }
            $state = $this->regionFactory->create()->load($region);
            $regionCode = $state['code'];
            if (preg_match($this->subscripHelper->cityMilitaryRegexString(), $city)
                && ($regionCode != "AP" && $regionCode != "AE" && $regionCode != "AA")) {
                $response = [
                    'success' => false,
                    'message' => $this->subscripHelper->addressErrorMilitaryMessage()
                ];
                return $resultJson->setData($response);
            } else if (($regionCode == "AP" || $regionCode == "AE" || $regionCode == "AA")
                && !preg_match($this->subscripHelper->cityMilitaryRegexString(), $city)) {
                $response = [
                    'success' => false,
                    'message' => $this->subscripHelper->addressErrorMilitaryMessage()
                ];
                return $resultJson->setData($response);
            } else {
                if ($streetAddress) {
                    $address = $streetAddress.",".$city.",".$regionCode.",".$countryCode;
                    if (!$this->geoCode($address)
                        && !preg_match($this->subscripHelper->pOBoxRegexString(), $streetAddress)) {
                        $response = [
                            'success' => false,
                            'message' => $this->subscripHelper->addressErrorMessage()
                        ];
                        return $resultJson->setData($response);
                    }
                }
            }

            $this->subscriptionRepository->save($model);
            $this->_eventManager->dispatch(
                Data::SUBSCRIPTION_SAVE_SUCCESSFULLY_EVENT,
                [Data::EVENT_DATA_KYE_SUBSCRIPTION => $model]
            );
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $this->subscripHelper->invalidErrorMessage()];
            $this->logger->error($e);
        }
        return $resultJson->setData($response);
    }

    /**
     * Check Street Address if the address can be found
     *
     * @param string $address
     * @return bool
     */
    public function geoCode($address)
    {

        $address = urlencode($address);
        $key = $this->subscripHelper->googleAPIKey();
        $url = "https://maps.google.com/maps/api/geocode/json";
        $query = [
            'key' => $key,
            'address' => $address,
        ];
        $dynamicUrl = $url.'?'.http_build_query($query);
        /* Create curl factory */
        $httpAdapter = $this->curl->create();
        $httpAdapter->write(
            \Zend_Http_Client::GET,
            $dynamicUrl,
            '1.1',
            ["Content-Type:application/json"]
        );
        $result = $httpAdapter->read();
        $body = \Zend_Http_Response::extractBody($result);
        /* convert JSON to Array */
        $resp = $this->jsonHelper->jsonDecode($body);
        if ($resp['status'] !== 'OK') {
            return false;
        }
        $resultArray = [];
        foreach ($resp['results'] as $res) {
            foreach ($res["address_components"] as $addressComponent) {
                $resultArray[] = $addressComponent['types'][0];
            }

            if (!in_array("street_number", $resultArray) || !in_array("route", $resultArray)) {
                return false;
            }
        }
        return true;
    }
}
