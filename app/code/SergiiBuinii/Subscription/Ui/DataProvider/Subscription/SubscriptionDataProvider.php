<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Ui\DataProvider\Subscription;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class SubscriptionDataProvider extends DataProvider
{
    /**
     * @var array $loadedData
     */
    protected $loadedData = [];

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = parent::getData();

        if (!empty($this->loadedData['items'])) {
            array_filter($this->loadedData['items'], function ($v, $k) {
                $this->loadedData['items'][$k] = $v;
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $this->loadedData;
    }
}
