<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Partner\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IoType
 */
class IoType implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** TODO: Refactor */
        return [
            [
                'label' => 'HTTP',
                'value' => 0,
            ],
            [
                'label' => 'FTP',
                'value' => 1,
            ],
            [
                'label' => 'SFTP',
                'value' => 2,
            ]
        ];
    }
}
