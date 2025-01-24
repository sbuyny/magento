<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Partner\Source;

use SergiiBuinii\PartnerFeed\Model\Partner;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Model\Feed
     */
    protected $feed;

    /**
     * Status constructor.
     * @param \SergiiBuinii\PartnerFeed\Model\Partner $feed
     */
    public function __construct(Partner $feed)
    {
        $this->feed = $feed;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->feed->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
