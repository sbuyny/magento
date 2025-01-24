<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Partner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use SergiiBuinii\PartnerFeed\Service\Feed\FeedService;

/**
 * Class Download
 */
class Download extends Action
{
    /**
     * Resource
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::partner_download';

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * @var \SergiiBuinii\PartnerFeed\Service\Feed\FeedService
     */
    protected $feedService;

    /**
     * Download constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param \SergiiBuinii\PartnerFeed\Service\Feed\FeedService $feedService
     */
    public function __construct(
        Context $context,
        PartnerRepository $partnerRepository,
        FeedService $feedService
    ) {
        $this->partnerRepository = $partnerRepository;
        $this->feedService = $feedService;
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
        $partnerId = $this->getRequest()->getParam('partner_id');
        if ($partnerId) {
            try {
                $partner = $this->partnerRepository->getById($partnerId);
                $this->downloadAndProceed($partner);
                $this->messageManager->addSuccessMessage(__('Download success.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Partner to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Dowload and Proceed
     *
     * @param \SergiiBuinii\PartnerFeed\Model\Partner $item
     */
    private function downloadAndProceed($item)
    {
        $params = $item->toArray();
        $this->feedService->execute($params);
    }
}
