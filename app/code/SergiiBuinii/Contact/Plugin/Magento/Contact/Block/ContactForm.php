<?php

namespace SergiiBuinii\Contact\Plugin\Magento\Contact\Block;

use SergiiBuinii\Contact\Model\Topic;
use Magento\Contact\Block\ContactForm as OriginalClass;

class ContactForm
{
    /**
     * Data key for topic list
     *
     * @type string
     */
    const TOPIC_BLOCK_DATA = 'topic_list';

    /**
     * @var \SergiiBuinii\Contact\Model\Topic $topic
     */
    private $topic;

    /**
     * ContactForm plugin constructor
     *
     * @param \SergiiBuinii\Contact\Model\Topic $topic
     */
    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * Extend contact block data
     *
     * @see \Magento\Contact\Block\ContactForm::getData()
     * @param \Magento\Contact\Block\ContactForm $subject
     * @param string $key
     * @param null $index
     * @return array
     */
    public function beforeGetData(OriginalClass $subject, $key = '', $index = null)
    {
        $subject->setData(self::TOPIC_BLOCK_DATA, $this->topic->getList());
        return [$key, $index];
    }
}
