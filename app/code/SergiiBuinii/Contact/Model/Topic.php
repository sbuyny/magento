<?php

namespace SergiiBuinii\Contact\Model;

use Magento\Framework\Serialize\SerializerInterface;
use SergiiBuinii\Contact\Helper\Config as ContactHelper;
use SergiiBuinii\Contact\Block\System\Config\Form\Field\Topic as TopicFormField;

class Topic
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \SergiiBuinii\Contact\Helper\Config
     */
    protected $contactConfig;

    /**
     * Data helper constructor
     *
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \SergiiBuinii\Contact\Helper\Config $contactConfig
     */
    public function __construct(SerializerInterface $serializer, ContactHelper $contactConfig)
    {
        $this->serializer = $serializer;
        $this->contactConfig = $contactConfig;
    }

    /**
     * Retrieve newsletter topic list
     *
     * @return array
     */
    public function getList()
    {
        $list = [];

        if ($topic = $this->contactConfig->getTopic()) {
            try {
                $unserialized = $this->serializer->unserialize($topic);
            } catch (\InvalidArgumentException $e) {
                $unserialized = [];
            }

            if (!empty($unserialized)) {
                foreach ($unserialized as $formField) {
                    $list[] = $formField[TopicFormField::TOPIC_FIELD_NAME];
                }
            }
        }

        return $list;
    }
}
