<?php
namespace Glew\Service\Model\Types;

class InventoryItem
{
    protected $objectManager;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }
    public function parse($product)
    {
        $stock = $this->objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
        $this->product_id = $product->getId();
        $this->qty = $stock->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
        $this->price = $product->getPrice();
        $this->cost = $product->getCost();
        return $this;
    }
}
