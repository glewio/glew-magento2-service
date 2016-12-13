<?php
namespace Glew\Service\Model\Types;

use Glew\Service\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;
use Glew\Service\Model\Types\RefundItemFactory;
use Magento\Framework\App\ResourceConnection;

class RefundItems {

    public $refundItems = array();
    protected $helper;
    protected $refundsCollection;
    protected $refundItemFactory;
    protected $resource;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $refundsCollection
     * @param \Glew\Service\Model\Types\RefundItemFactory $refundItemFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        Data $helper,
        CollectionFactory $refundsCollection,
        RefundItemFactory $refundItemFactory,
        ResourceConnection $resource
    ) {
        $this->helper = $helper;
        $this->refundsCollection = $refundsCollection;
        $this->refundItemFactory = $refundItemFactory;
        $this->resource = $resource;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        $salesFlatCredMemItem = $this->resource->getTableName('sales_creditmemo_item');
        if ($startDate && $endDate && !$id) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));

            $refunds = $this->refundsCollection->create()
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $refunds = $this->refundsCollection->create();
        }
        $refunds->addAttributeToFilter('main_table.store_id', $this->helper->getStore()->getStoreId());
        $refunds->getSelect()->join(array('credit_item' => $salesFlatCredMemItem), 'credit_item.parent_id = main_table.entity_id', array('*'));
        if ($id ) {
            $refunds->addAttributeToFilter('credit_item.entity_id', $id);
        }
        $refunds->setOrder('created_at', $sortDir);
        $refunds->setCurPage($pageNum);
        $refunds->setPageSize($pageSize);

        if ($refunds->getLastPageNumber() < $pageNum) {
            return $this;
        }

        $refundItem = $this->refundItemFactory->create();
        foreach ($refunds as $refund) {
            $model = $refundItem->parse($refund);
            if ($model) {
                $this->refundItems[] = $model;
            }
        }

        return $this->refundItems;
    }
}
