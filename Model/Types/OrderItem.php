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
        $this->price = ($orderItem->getPrice()) ? round($orderItem->getPrice(), 2) : null;
        $this->original_price = ($orderItem->getOriginalPrice()) ? round($orderItem->getOriginalPrice(), 2) : null;
        $this->cost = ($orderItem->getCost()) ? round($orderItem->getCost(), 2) : null;
        $this->row_total = ($orderItem->getRowTotal()) ? round($orderItem->getRowTotal(), 2) : null;
        $this->tax_percent = ($orderItem->getTaxPercent()) ? round($orderItem->getTaxPercent(), 2) : null;
        $this->tax_amount = ($orderItem->getTaxAmount()) ? round($orderItem->getTaxAmount(), 2) : null;
        $this->discount_percent = ($orderItem->getDiscountPercent()) ? round($orderItem->getDiscountPercent(), 2) : null;
        $this->discount_amount = ($orderItem->getDiscountAmount()) ? round($orderItem->getDiscountAmount(), 2) : null;
        $this->weight = ($orderItem->getWeight()) ? round($orderItem->getWeight(), 2) : null;
        $this->row_weight = ($orderItem->getRowWeight()) ? round($orderItem->getRowWeight(), 2) : null;
        $this->additional_data = $orderItem->getAdditionalData();
        $this->parent_item_id = $orderItem->getParentItemId();
        $this->base_cost = ($orderItem->getBaseCost()) ? round($orderItem->getBaseCost(), 2) : null;
        return $this;
    }
}
