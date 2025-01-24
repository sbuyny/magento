<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Catalog\Plugin\ViewModel\Product;

use Magento\Catalog\ViewModel\Product\Breadcrumbs as OriginalClass;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SergiiBuinii\ExtendCatalogUrlRewrite\Helper\Config as ExtendCatalogUrlRewriteConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Breadcrumbs
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \SergiiBuinii\ExtendCatalogUrlRewrite\Helper\Config
     */
    protected $extendCatalogUrlRewriteConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Init dependencies
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \SergiiBuinii\ExtendCatalogUrlRewrite\Helper\Config $extendCatalogUrlRewriteConfig
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ExtendCatalogUrlRewriteConfig $extendCatalogUrlRewriteConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->extendCatalogUrlRewriteConfig = $extendCatalogUrlRewriteConfig;
        $this->serializer = $serializer;
    }

    /**
     * Perform check if use category path in plp is enabled
     *
     * @return bool
     */
    public function isCategoryUsedInProductUrlPlp()
    {
        return $this->scopeConfig->isSetFlag(
            'catalog/seo/product_use_categories_plp',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Fix json config
     *
     * @param \Magento\Catalog\ViewModel\Product\Breadcrumbs $subject
     * @param $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsonConfigurationHtmlEscaped(OriginalClass $subject, $result)
    {
        $result = $this->serializer->unserialize($result);
        $result['breadcrumbs']['userCategoryPathInUrlPlp'] = $this->isCategoryUsedInProductUrlPlp();
        $result['breadcrumbs']['isSimplifyUrlEnabled'] = $this->extendCatalogUrlRewriteConfig->isSimplifyCategoryUrl();
        if (isset($result['breadcrumbs']['userCategoryPathInUrl'])) {
            $result['breadcrumbs']['useCategoryPathInUrl'] = $result['breadcrumbs']['userCategoryPathInUrl'];
            unset($result['breadcrumbs']['userCategoryPathInUrl']);
        }
        return $this->serializer->serialize($result);
    }
}
