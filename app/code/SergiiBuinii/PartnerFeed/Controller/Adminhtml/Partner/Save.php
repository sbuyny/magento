<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Partner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use SergiiBuinii\PartnerFeed\Model\PartnerFactory;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 */
class Save extends Action
{
    /**
     * Resource
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::partner_save';

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerFactory
     */
    protected $partnerFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerFactory $partnerFactory
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        PartnerFactory $partnerFactory,
        PartnerRepository $partnerRepository,
        DataPersistorInterface $dataPersistor
    ) {
        $this->partnerRepository = $partnerRepository;
        $this->partnerFactory = $partnerFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $entityId = $this->getRequest()->getParam(PartnerInterface::ENTITY_ID);
            if ($entityId) {
                try {
                    $model = $this->partnerRepository->getById($entityId);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('This Partner no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $data[PartnerInterface::ENTITY_ID] = null;
                $model = $this->partnerFactory->create();
            }

            $model->setData($data);

            try {
                $this->partnerRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Partner.'));
                $this->dataPersistor->clear('SergiiBuinii_feed_partner');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [PartnerInterface::ENTITY_ID => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Partner.'));
            }

            $this->dataPersistor->set('SergiiBuinii_feed_partner', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                [PartnerInterface::ENTITY_ID => $this->getRequest()->getParam(PartnerInterface::ENTITY_ID)]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
