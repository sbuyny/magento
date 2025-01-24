<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml\Partner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory;

/**
 * Class MassDelete
 */
class MassDelete extends Action
{
    /**
     * Resource
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::partner_delete';
    
    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * MassDelete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory $collectionFactory
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PartnerRepository $partnerRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->partnerRepository = $partnerRepository;
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
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $this->partnerRepository->delete($item);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
