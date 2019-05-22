<?php
namespace Glew\Service\Model\Types;

class OrderItems
{
    public $orderItems = array();
    private $pageNum;
    protected $helper;
    protected $orderItemsFactory;
    protected $objectManager;
    protected $eavConfig;
    protected $resource;
    protected $productMetadata;

    /**
     * @param \Glew\Service\Helper\Data                                       $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemsFactory
     * @param \Magento\Framework\ObjectManagerInterface                       $objectManager
     * @param \Magento\Eav\Model\Config                                       $eavConfig
     * @param \Magento\Framework\App\ResourceConnection                       $resource
     * @param \Magento\Framework\App\ProductMetadataInterface                 $productMetadata
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemsFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->helper = $helper;
        $this->orderItemsFactory = $orderItemsFactory;
        $this->objectManager = $objectManager;
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
        $this->productMetadata = $productMetadata;
    }
    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $store = $this->helper->getStore();
        $edition = $this->productMetadata->getEdition();
        $this->pageNum = $pageNum;
        $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cost');
        if ($id) {
            $collection = $this->orderItemsFactory->create()
                ->addAttributeToFilter('main_table.item_id', $id);
        } elseif ($startDate && $endDate) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));
            $collection = $this->orderItemsFactory->create()
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $collection = $this->orderItemsFactory->create();
        }
        $collection->addAttributeToFilter('main_table.store_id', $this->helper->getStore()->getStoreId());
        $catProdEntDecTable = $this->resource->getTableName('catalog_product_entity_decimal');
        if ($edition === 'Community') {
            $collection->getSelect()->joinLeft(
              array('cost' => $catProdEntDecTable),
              "main_table.product_id = cost.entity_id AND cost.attribute_id = {$attribute->getId()} AND cost.store_id = {$store->getStoreId()}",
              array('cost' => 'value')
          );
        } else {
            $collection->getSelect()->joinLeft(
              array('cost' => $catProdEntDecTable),
              "main_table.product_id = cost.row_id AND cost.attribute_id = {$attribute->getId()} AND cost.store_id = {$store->getStoreId()}",
              array('cost' => 'value')
          );
        }
        $collection->setOrder('created_at', $sortDir);
        $collection->setCurPage($pageNum);
        $collection->setPageSize($pageSize);
        if ($collection->getLastPageNumber() < $pageNum) {
            return $this;
        }
        foreach ($collection as $orderItem) {
            $continue = true;
            if ($orderItem && $orderItem->getId()) {
                if ($orderItem->getParentItemId()) {
                    foreach ($this->orderItems as $key => $oi) {
                        if ($orderItem->getParentItemId() == $this->orderItems[$key]->order_item_id) {
                            $this->orderItems[$key]->product_id = $orderItem->getProductId();
                            $continue = false;
                        }
                    }
                    if (!$continue) {
                        continue;
                    }
                }
                $model = $this->objectManager->create('\Glew\Service\Model\Types\OrderItem')->parse($orderItem);
                if ($model) {
                    $this->orderItems[] = $model;
                }
            }
        }
        return $this->orderItems;
    }
}
