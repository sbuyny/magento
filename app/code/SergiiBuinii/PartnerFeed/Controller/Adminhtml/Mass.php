<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use SergiiBuinii\PartnerFeed\Model\FeedRepository;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory;

/**
 * Class Mass
 */
abstract class Mass extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::feed_mass';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedRepository
     */
    protected $feedRepository;

    /**
     * Delete flag
     *
     * @var bool
     */
    protected $deleteAction = false;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory
     */
    protected $feedCollectionFactory;

    /**
     * Retrieve success message after action
     *
     * @return string
     */
    abstract protected function getSuccessMsg();

    /**
     * Mass constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\FeedRepository $feedRepository
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory $feedCollectionFactory
     */
    public function __construct(
        Filter $filter,
        Action\Context $context,
        FeedRepository $feedRepository,
        CollectionFactory $feedCollectionFactory
    ) {
        $this->filter = $filter;
        $this->feedRepository = $feedRepository;
        $this->feedCollectionFactory = $feedCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->feedCollectionFactory->create()
        );
        $size = $collection->getSize();

        if ($this->deleteAction) {
            foreach ($collection as $item) {
                $this->feedRepository->delete($item);
            }
        } else {
            foreach ($collection as $item) {
                $item->setStatus($this->statusCode);
                $this->feedRepository->save($item);
            }
        }

        $this->messageManager->addSuccess(__($this->getSuccessMsg(), $size));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererUrl();
    }
}
