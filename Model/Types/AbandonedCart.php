<?php
namespace Glew\Service\Model\Types;

class AbandonedCart {

    protected $helper;
    protected $group;
    protected $objectManager;

    /**
     * @param \Magento\Customer\Model\Group $group
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Customer\Model\Group $group,
        \Glew\Service\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->group = $group;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
    }

    public function parse($cart)
    {
        $products = array();

        foreach ($cart->getAllItems() as $item) {
            $obj = new \stdClass();
            $obj->product_id = $item->getProduct()->getId();
            $obj->qty = $item->getQty();
            $products[] = $obj;
        }

        $this->id = $cart->getId();
        $this->email = $cart->getCustomerEmail();
        $this->customer_id = $cart->getCustomerId() ? $cart->getCustomerId() : 0;
        $this->customer_group_id = $cart->getCustomerGroupId() ? $cart->getCustomerGroupId() : null;
        $customerGroup = $this->group->load($cart->getCustomerGroupId());
        $this->customer_group = $customerGroup->getCode();
        $this->created_at = $cart->getCreatedAt();
        $this->updated_at = $cart->getUpdatedAt();
        $this->items_count = $cart->getItemsCount();
        $this->items_qty = $cart->getItemsQty();
        $this->products = $products;
        $this->total = round($cart->getSubtotal(), 2);

        $this->discount_amount = round($cart->getDiscountAmount(), 2);
        $this->discount_description = $cart->getDiscountDescription();
        $this->coupon_code = $cart->getCouponCode();
        $this->weight = $cart->getWeight();
        $this->remote_ip = $cart->getRemoteIp();
        $this->store = $cart->getStore()->getCode();
        $this->currency = $cart->getQuoteCurrencyCode();

        return $this;
    }
}
