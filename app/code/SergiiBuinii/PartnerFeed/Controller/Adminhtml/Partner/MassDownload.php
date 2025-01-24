<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Partner;

use SergiiBuinii\PartnerFeed\Controller\Adminhtml\MassPartner;
use SergiiBuinii\PartnerFeed\Model\FeedRepository;
use Magento\Backend\App\Action\Context;
use SergiiBuinii\PartnerFeed\Model\Partner;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory as PartnerCollectionFactory;
use SergiiBuinii\PartnerFeed\Service\Feed\FeedService;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDownload
 */
class MassDownload extends MassPartner
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Service\Feed\FeedService
     */
    protected $feedService;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory
     */
    protected $partnerCollectionFactory;

    /**
     * MassDownload constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory $partnerCollectionFactory
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory $feedCollectionFactory
     * @param \SergiiBuinii\PartnerFeed\Service\Feed\FeedService $feedService
     */
    public function __construct(
        Filter $filter,
        Context $context,
        PartnerRepository $partnerRepository,
        PartnerCollectionFactory $partnerCollectionFactory,
        CollectionFactory $feedCollectionFactory,
        FeedService $feedService
    ) {
        parent::__construct($filter, $context, $partnerRepository, $partnerCollectionFactory);
        $this->feedService = $feedService;
        $this->partnerCollectionFactory = $partnerCollectionFactory;
    }

    /**
     * Retrieve success message after action
     *
     * @return string
     */
    protected function getSuccessMsg()
    {
        return 'A total of %1 record(s) have been processed.';
    }

    /**
     * Execute download
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->partnerCollectionFactory->create()
        );
        $size = $collection->getSize();

        foreach ($collection as $item) {
            try {
                $this->downloadAndProceed($item);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                continue;
            }
        }

        $this->messageManager->addSuccessMessage(__($this->getSuccessMsg(), $size));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Download and proceed
     *
     * @param \SergiiBuinii\PartnerFeed\Model\Partner $item
     */
    private function downloadAndProceed($item)
    {
        $params = $item->toArray();
        $this->feedService->execute($params);
    }
}
