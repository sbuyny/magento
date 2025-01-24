<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SergiiBuinii\PartnerFeed\Model\FeedFactory;
use SergiiBuinii\PartnerFeed\Model\FeedRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 */
class Save extends Action
{
    /**
     * Resource
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::feed_save';

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedRepository
     */
    protected $feedRepository;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\FeedFactory $feedFactory
     * @param \SergiiBuinii\PartnerFeed\Model\FeedRepository $feedRepository
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        FeedFactory $feedFactory,
        FeedRepository $feedRepository,
        DataPersistorInterface $dataPersistor
    ) {
        $this->feedRepository = $feedRepository;
        $this->feedFactory = $feedFactory;
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
            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                try {
                    $model = $this->feedRepository->getById($id);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('This Feed no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $data['entity_id'] = null;
                $model = $this->feedFactory->create();
            }

            $model->setData($data);

            try {
                $this->feedRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Feed.'));
                $this->dataPersistor->clear('SergiiBuinii_feed_feed');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Feed.'));
            }

            $this->dataPersistor->set('SergiiBuinii_feed_feed', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
