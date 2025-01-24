<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Controller\Adminhtml\Subscription;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\Subscription\Model\SubscriptionRepository;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription\CollectionFactory;

class MassDelete extends Action
{
    /**
     * Authorization level of a admin subscription manage
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_Subscription::manage_subscription';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \SergiiBuinii\Subscription\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \SergiiBuinii\Subscription\Model\SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * MassDelete constructor
     *
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\Subscription\Model\ResourceModel\Subscription\CollectionFactory $collectionFactory
     * @param \SergiiBuinii\Subscription\Model\SubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        Filter $filter,
        Action\Context $context,
        CollectionFactory $collectionFactory,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        parent::__construct($context);
    }

    /**
     * MassDelete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $size = $collection->getSize();

        foreach ($collection as $item) {
            $this->subscriptionRepository->delete($item);
        }

        if ($size) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $size)
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('SergiiBuinii_subscription/*/');
    }
}
