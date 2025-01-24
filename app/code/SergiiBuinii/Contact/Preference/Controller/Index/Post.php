<?php

namespace SergiiBuinii\Contact\Preference\Controller\Index;

use Magento\Contact\Controller\Index\Post as BaseClass;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use SergiiBuinii\Contact\Helper\Config;
use Potato\Zendesk\Model\Authorization;
use Magento\Store\Model\StoreManagerInterface;
use Potato\Zendesk\Api\TicketManagementInterface as TicketManagement;

class Post extends BaseClass
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \SergiiBuinii\Contact\Helper\Config
     */
    private $configHelper;

    /** @var \Potato\Zendesk\Model\Authorization  */
    protected $authorization;

    /** @var  \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var  \Potato\Zendesk\Api\TicketManagementInterface  */
    protected $ticketManagement;

    /**
     * Post constructor.
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param MailInterface $mail
     * @param DataPersistorInterface $dataPersistor
     * @param Config $configHelper
     * @param Authorization $authorization
     * @param StoreManagerInterface $storeManager
     * @param TicketManagement $ticketManagement
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        MailInterface $mail,
        DataPersistorInterface $dataPersistor,
        Config $configHelper,
        Authorization $authorization,
        StoreManagerInterface $storeManager,
        TicketManagement $ticketManagement,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context, $contactsConfig, $mail, $dataPersistor, $logger);
        $this->context = $context;
        $this->mail = $mail;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->configHelper = $configHelper;
        $this->authorization = $authorization;
        $this->storeManager = $storeManager;
        $this->ticketManagement = $ticketManagement;
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            $this->sendEmail($this->validatedParams());
            $this->messageManager->addSuccessMessage(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('contact_us');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
        }
        if ($this->configHelper->getRedirectUrl()) {
            return $this->resultRedirectFactory->create()->setUrl($this->configHelper->getRedirectUrl());
        }
        return $this->resultRedirectFactory->create()->setPath('contact/index');
    }

    /**
     * Send Email
     *
     * @param array $post Post data from contact form
     * @return void
     */
    private function sendEmail($post)
    {
        $store = $this->storeManager->getStore();
        $postData = [];
        $postData['subject'] = $post['contactType'];
        $postData['order_increment'] = "0";
        $postData['comment'] = $post['comment'];
        $attachments = [];

        try {
            $post["from_contact"] = true;
            $ticket = $this->ticketManagement->createTicket($postData, $store, $attachments, $post);
            if(isset($ticket->ticket->id)){
                $post['contactType'] = "Ticket# ".$ticket->ticket->id.": ".$post['contactType'];
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            //$this->messageManager->addExceptionMessage($e, __('Something went wrong while create the ticket.'));
        }
    }

    /**
     * Validate params
     *
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('firstname')) === '') {
            throw new LocalizedException(__('Enter the First Name and try again.'));
        }
        if (trim($request->getParam('lastname')) === '') {
            throw new LocalizedException(__('Enter the Last Name and try again.'));
        }
        if (trim($request->getParam('comment')) === '') {
            throw new LocalizedException(__('Enter the comment and try again.'));
        }
        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
        if (trim($request->getParam('hideit')) !== '') {
            throw new \Exception();
        }

        return $request->getParams();
    }
}
