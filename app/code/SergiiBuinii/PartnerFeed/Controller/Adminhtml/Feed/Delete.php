<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SergiiBuinii\PartnerFeed\Model\FeedRepository;

/**
 * Class Delete
 */
class Delete extends Action
{
    /**
     * Resource
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::feed_delete';

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedRepository
     */
    protected $feedRepository;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\FeedRepository $feedRepository
     */
    public function __construct(
        Context $context,
        FeedRepository $feedRepository
    ) {
        $this->feedRepository = $feedRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->feedRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the Feed.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Feed to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
