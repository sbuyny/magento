<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */
namespace SergiiBuinii\CustomerDonation\ViewModel;

use SergiiBuinii\CustomerDonation\Service\SessionService;
use Magento\Catalog\Helper\Image;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\Helper\Data;

/**
 * Donation class
 *
 * Contains additional functionality for blocks
 * It is simple example of ViewModel
 */
class Donation implements ArgumentInterface
{
    /**
     * @var \SergiiBuinii\CustomerDonation\Service\SessionService
     */
    protected $sessionService;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $image;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $price;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cart;

    /**
     * Donation constructor
     *
     * @param \SergiiBuinii\CustomerDonation\Service\SessionService $sessionService
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Catalog\Helper\Image $image
     * @param \Magento\Framework\Pricing\Helper\Data $price
     * @param \Magento\Checkout\Helper\Cart $cart
     */
    public function __construct(
        SessionService $sessionService,
        Json $json,
        Image $image,
        Data $price,
        Cart $cart
    ) {
        $this->sessionService = $sessionService;
        $this->json = $json;
        $this->image = $image;
        $this->price = $price;
        $this->cart = $cart;
    }

    /**
     * Check if donation widget available
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasDonation()
    {
        return $this->sessionService->hasDonation();
    }

    /**
     * Retrieve widget items
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItems()
    {
        $items = [];
        foreach ($this->sessionService->getDonationItems() as $item) {
            $id = $item->getId();
            $itemData = [
                'id'                => $id,
                'image'             => $this->image
                    ->init($item, 'product_thumbnail_image')
                    ->setImageFile($item->getFile())
                    ->getUrl(),
                'name'              => $item->getName(),
                'description'       => $item->getDescription(),
                'price'             => $this->price->currency($item->getPrice(), true, false),
                'add_to_cart_url'   => $this->cart->getAddUrl($item)
            ];
            $items[$id] = $itemData;
        }
        return $this->json->serialize($items);
    }
}
