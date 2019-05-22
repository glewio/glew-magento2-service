<?php
namespace Glew\Service\Model\Types;

class Orders
{
    public $orders = array();
    protected $helper;
    protected $orderCollection;
    protected $objectManager;
    private $pageNum;
    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->orderCollection = $orderCollection;
        $this->objectManager = $objectManager;
    }
    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if ($id) {
            $collection = $this->orderCollection->create()
                ->addAttributeToFilter('main_table.increment_id', $id);
        } elseif ($startDate && $endDate) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));
            $collection = $this->orderCollection->create()
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $collection = $this->orderCollection->create();
        }
        $collection->addAttributeToFilter('main_table.store_id', $this->helper->getStore()->getStoreId());
        $collection->addAttributeToSort('created_at', $sortDir);
        $collection->setCurPage($pageNum);
        $collection->setPageSize($pageSize);
        if ($collection->getLastPageNumber() < $pageNum) {
            return $this;
        }
        foreach ($collection as $order) {
            if ($order && $order->getId()) {
                $model = $this->objectManager->create('\Glew\Service\Model\Types\Order')->parse($order);
                if ($model) {
                    $this->orders[] = $model;
                }
            }
        }
        return $this->orders;
    }
}
