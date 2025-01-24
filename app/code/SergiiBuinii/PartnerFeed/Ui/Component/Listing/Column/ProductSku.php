<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;

class ProductSku extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ProductSku constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ProductRepositoryInterface $productRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[FeedInterface::SKU])) {
                    $sku = $item[FeedInterface::SKU];
                    $product = $this->productRepository->get($sku);
                    $id = $product->getId();
                    $url = $this->urlBuilder->getUrl('catalog/product/edit', ['id' => $id]);
                    $item[FeedInterface::SKU] = html_entity_decode("<a href='$url' target='_blank'>$sku</a>");
                }
            }
        }

        return $dataSource;
    }
}
