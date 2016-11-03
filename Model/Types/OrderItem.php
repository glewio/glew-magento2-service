<?php
namespace Glew\Service\Model\Types;

class OrderItem {

    protected $helper;
    protected $objectManager;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->objectManager = $objectManager;
    }

    public function parse($orderItem) {

        $this->order_item_id = $orderItem->getId();
        $this->order_id = $orderItem->getOrderId();
        $this->created_at = $orderItem->getCreatedAt();
        $this->updated_at = $orderItem->getUpdatedAt();
        $this->weight = $orderItem->getWeight();
        $this->sku = $orderItem->getSku();
        $this->product_id = $orderItem->getProductId();
        $this->name = $orderItem->getName();
        $this->description = $orderItem->getDescription();
        $this->visibility = '';
        $this->brand = '';
        $this->website_names = '';
        $this->store = $orderItem->getOrder()->getStore()->getCode();

        $this->qty_ordered = (int) $orderItem->getQtyOrdered();
        $this->qty_refunded = (int) $orderItem->getQtyRefunded();
        $this->qty_shipped = (int) $orderItem->getQtyShipped();
        $this->qty_backordered = (int) $orderItem->getQtyBackordered();

        $this->price = round($orderItem->getPrice(), 2);
        $this->original_price = round($orderItem->getOriginalPrice(), 2);
        $this->cost = round($orderItem->getCost(), 2);
        $this->row_total = round($orderItem->getRowTotal(), 2);
        $this->tax_percent = round($orderItem->getTaxPercent(), 2);
        $this->tax_amount = round($orderItem->getTaxAmount(), 2);
        $this->discount_percent = round($orderItem->getDiscountPercent(), 2);
        $this->discount_amount = round($orderItem->getDiscountAmount(), 2);

        $this->weight = round($orderItem->getWeight(), 2);
        $this->row_weight = round($orderItem->getRowWeight(), 2);
        $this->additional_data = $orderItem->getAdditionalData();

        return $this;
    }

}
