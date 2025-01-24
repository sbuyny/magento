<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Controller\Adminhtml\Subscription;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use SergiiBuinii\Subscription\Model\SubscriptionRepository;

class Delete extends Action
{
    /**
     * Authorization level of a admin subscriptions manage
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SergiiBuinii_Subscription::manage_subscription';

    /**
     * @var \SergiiBuinii\Subscription\Model\SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * Delete constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SergiiBuinii\Subscription\Model\SubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        Action\Context $context,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->subscriptionRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the subscription.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t find a subscription to delete.'));
            }
        }

        return $resultRedirect->setPath('SergiiBuinii_subscription/subscription/');
    }
}
