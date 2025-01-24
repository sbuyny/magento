<?php

namespace SergiiBuinii\Contact\Preference\Model\Management;

use Potato\Zendesk\Model\Management\Ticket as PotatoTicket;
use Potato\Zendesk\Model\Authorization;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Potato\Zendesk\Model\Config;
use Potato\Zendesk\Api\Data\TicketInterfaceFactory;
use Potato\Zendesk\Api\Data\MessageInterfaceFactory;
use Potato\Zendesk\Api\Data\AttachmentInterfaceFactory;
use Potato\Zendesk\Api\Data\UserInterfaceFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\Store;
use Magento\Sales\Api\OrderRepositoryInterface;
use Zendesk\API\HttpClient as ZendeskAPI;
use Psr\Log\LoggerInterface;

/**
 * Class Customer
 */
class Ticket extends  PotatoTicket
{
    /** @var \Potato\Zendesk\Model\Authorization  */
    protected $authorization;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var  \Potato\Zendesk\Api\Data\TicketInterfaceFactory */
    protected $ticketFactory;

    /** @var \Potato\Zendesk\Api\Data\MessageInterfaceFactory  */
    protected $messageFactory;

    /** @var \Potato\Zendesk\Api\Data\AttachmentInterfaceFactory  */
    protected $attachmentFactory;

    /** @var \Potato\Zendesk\Api\Data\UserInterfaceFactory  */
    protected $userFactory;

    /** @var \Magento\Customer\Model\Session  */
    protected $customerSession;

    /** @var \Magento\Sales\Api\OrderRepositoryInterface  */
    protected $orderRepository;
    
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Potato\Zendesk\Model\Config  */
    protected $config;

    /**
     * Ticket constructor.
     * @param \Potato\Zendesk\Model\Authorization $authorization
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Potato\Zendesk\Api\Data\TicketInterfaceFactory $ticketInterfaceFactory
     * @param \Potato\Zendesk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory
     * @param \Potato\Zendesk\Api\Data\AttachmentInterfaceFactory $attachmentInterfaceFactory
     * @param \Potato\Zendesk\Api\Data\UserInterfaceFactory $userInterfaceFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Potato\Zendesk\Model\Config $config
     */
    public function __construct(
        Authorization $authorization,
        CustomerRepositoryInterface $customerRepository,
        TicketInterfaceFactory $ticketInterfaceFactory,
        MessageInterfaceFactory $messageInterfaceFactory,
        AttachmentInterfaceFactory $attachmentInterfaceFactory,
        UserInterfaceFactory $userInterfaceFactory,
        CustomerSession $customerSession,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        Config $config
    ) {
        parent::__construct(
            $authorization,
            $customerRepository,
            $ticketInterfaceFactory,
            $messageInterfaceFactory,
            $attachmentInterfaceFactory,
            $userInterfaceFactory,
            $customerSession,
            $orderRepository,
            $logger,
            $config
        );
    }


    /**
     * @param array $ticketData
     * @param int|Store|null $store
     * @param array $attachments
     * @return null|\stdClass
     * @throws \Exception
     */
    public function createTicket($ticketData, $store, $attachments = [], $post = [])
    {
        $client = $this->authorization->connectToZendesk($store);
        if (null === $client) {
            throw new \Exception(__('Authorization to Zendesk failed'));
        }
        $attachmentList = $this->prepareAttachments($client, $attachments);
        if (null !== $customer = $this->getCustomer()) {
            $email = $customer->getEmail();
            $name = $customer->getFirstname() . ' ' . $customer->getLastname();
        } elseif (isset($post['firstname']) && isset($post['lastname']) && isset($post['email'])
            && isset($post["from_contact"])) {
            $email = $post['email'];
            $name = $post['firstname'] . ' ' . $post['lastname'];
        } elseif (array_key_exists('order_id', $ticketData)) {
            $order = $this->orderRepository->get($ticketData['order_id']);
            $email = $order->getCustomerEmail();
            $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        } elseif (array_key_exists('id', $ticketData)) {
            $customer = $this->customerRepository->getById($ticketData['id']);
            $email = $customer->getEmail();
            $name = $customer->getFirstname() . ' ' . $customer->getLastname();
        } else {
            throw new \Exception(__('Customer not found'));
        }

        $tag = null;
        $subject = null;

        if ($this->config->isSubjectFieldDropdown()) {
            $tag = $ticketData['subject'];
            $subjectFields = $this->config->getSubjectDropdownContent();
            if (array_key_exists($tag,$subjectFields)) {
                $subject = $subjectFields[$tag];
            }
        } else {
            $subject = $ticketData['subject'];
        }
        if(isset($post['contactType']) && !$subject){
            $subject = $subject.$post['contactType'];
        }
        $params = [
            'comment'  => [
                'html_body' => $ticketData['comment']
            ],
            'subject'  => $subject,
            'requester' => [
                'name' => $name,
                'email' => $email,
            ]
        ];
        
        if ($tag) {
            $params['tags'][] = $tag;
        }
        if ($orderFieldId = $this->config->getOrderNumberFieldId()) {
            if (array_key_exists('order_increment', $ticketData) && !empty($ticketData['order_increment'])) {
                $params['custom_fields'][] = [
                    'id' => $orderFieldId,
                    'value' => $ticketData['order_increment']
                ];
            } elseif (array_key_exists('order_id', $ticketData)) {
                $order = $this->orderRepository->get($ticketData['order_id']);
                $params['custom_fields'][] = [
                    'id' => $orderFieldId,
                    'value' => $order->getIncrementId()
                ];
            }
        }

        if (!empty($attachmentList)) {
            $params['comment']['uploads'] = $attachmentList;
        }
        return $client->tickets()->create($params);
    }

    /**
     * @param ZendeskAPI $client
     * @param array $attachments
     * @return array
     */
    private function prepareAttachments($client, $attachments = [])
    {
        $attachmentList = [];
        if (empty($attachments) || !array_key_exists('error', $attachments)) {
            return $attachmentList;
        }
        foreach ($attachments["error"] as $key => $error) {
            if ($error !== UPLOAD_ERR_OK) {
                continue;
            }
            $uploadedFile = $client->attachments()->upload([
                'file' => $attachments['tmp_name'][$key],
                'type' => $attachments['type'][$key],
                'name' => $attachments['name'][$key]
            ]);
            $attachmentList[] = $uploadedFile->upload->token;

        }
        return $attachmentList;
    }
}
