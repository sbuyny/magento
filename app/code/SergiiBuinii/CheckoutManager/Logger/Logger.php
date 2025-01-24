<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CheckoutManager\Logger;

use SergiiBuinii\CheckoutManager\Helper\Config;

/**
 * CheckoutManager Logger Class Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * @var \SergiiBuinii\CheckoutManager\Helper\Config $helper
     */
    private $helper;

    /**
     * Logger constructor.
     * @param \SergiiBuinii\CheckoutManager\Helper\Config $helper
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        Config $helper,
        $name,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);

        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function critical($message, array $context = [])
    {
        return $this->_log($message, $context, 'critical', '[ ERROR ]');
    }

    /**
     * @inheritdoc
     */
    public function info($message, array $context = [])
    {
        return $this->_log($message, $context, 'info', '[ INFO ]');
    }

    /**
     * @inheritdoc
     */
    public function debug($message, array $context = [])
    {
        return $this->_log($message, $context, 'debug', '[ DEBUG ]');
    }

    /**
     * @inheritdoc
     */
    public function warn($message, array $context = [])
    {
        return $this->_log($message, $context, 'warn', '[ WARN ]');
    }

    /**
     * @inheritdoc
     */
    public function success($message, array $context = [])
    {
        return $this->_log($message, $context, 'info', '[ SUCCESS ]');
    }

    /**
     * @inheritdoc
     */
    public function fail($message, array $context = [])
    {
        return $this->_log($message, $context, 'info', '[ FAIL ]');
    }

    /**
     * @inheritdoc
     */
    public function force($message, array $context = [])
    {
        return $this->_log($message, $context, 'info', '[ FORCING ]');
    }

    // @codingStandardsIgnoreStart
    /**
     * Log message
     *
     * @param array|string $message
     * @param array $context
     * @param string $parent
     * @param string $level
     * @return bool
     */
    protected function _log($message, array $context = [], $parent = 'info', $level = '[ INFO ]')
    {
        if (!$this->helper->isDebugEnabled()) {
            return false;
        }

        if ($message instanceof \Exception) {
            $message = $message->getMessage() . "\n" . $message->getTraceAsString();
        }

        return parent::$parent($level . ' ' . $this->arrayToString($message), $context);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Convert array to string
     *
     * @param array|string $message
     *
     * @return string
     */
    private function arrayToString($message)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        return $message;
    }
}