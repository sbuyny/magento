<?php

namespace SergiiBuinii\Catalog\ViewModel;

class Media implements \Magento\Framework\View\Element\Block\ArgumentInterface {
	/**
	 * @var \Magento\Store\Model\Store
	 */
	protected $_store;

	/**
	 * Media constructor.
	 *
	 * @param \Magento\Store\Model\Store $store
	 */
	public function __construct(
		\Magento\Store\Model\Store $store
	)
	{
		$this->_store = $store;
	}

	/**
	 * @return string
	 */
	public function getMediaUrl()
	{
		return $this->_store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
}
