<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Block\Adminhtml\Partner\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DownloadButton
 */
class DownloadButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Retrieve main button data
     *
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getModelId()) {
            return [
                'label' => __('Download Feeds'),
                'on_click' => sprintf("location.href = '%s';", $this->getDownloadUrl()),
                'class' => 'primary',
                'sort_order' => 10
            ];
        }
        return [];
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->getUrl('*/*/download', ['partner_id' => $this->getModelId()]);
    }
}
