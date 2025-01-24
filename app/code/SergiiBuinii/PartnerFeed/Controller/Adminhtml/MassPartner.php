<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory;

/**
 * Class MassPartner
 */
abstract class MassPartner extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_PartnerFeed::partner_mass';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * Delete flag
     *
     * @var bool
     */
    protected $deleteAction = false;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory
     */
    protected $partnerCollectionFactory;

    /**
     * Retrieve success message after action
     *
     * @return string
     */
    abstract protected function getSuccessMsg();

    /**
     * MassPartner constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory $partnerCollectionFactory
     */
    public function __construct(
        Filter $filter,
        Action\Context $context,
        PartnerRepository $partnerRepository,
        CollectionFactory $partnerCollectionFactory
    ) {
        $this->filter = $filter;
        $this->partnerRepository = $partnerRepository;
        $this->partnerCollectionFactory = $partnerCollectionFactory;
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
            $this->partnerCollectionFactory->create()
        );
        $size = $collection->getSize();

        if ($this->deleteAction) {
            foreach ($collection as $item) {
                $this->partnerRepository->delete($item);
            }
        } else {
            foreach ($collection as $item) {
                $item->setStatus($this->statusCode);
                $this->partnerRepository->save($item);
            }
        }

        $this->messageManager->addSuccess(__($this->getSuccessMsg(), $size));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererUrl();
    }
}
