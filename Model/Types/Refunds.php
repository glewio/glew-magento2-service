<?php
namespace Glew\Service\Model\Types;

class Refunds {

    public $refunds = array();
    protected $helper;
    protected $refundsCollection;
    protected $objectManager;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $refundsCollection
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $refundsCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->refundsCollection = $refundsCollection;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;

        if ($id) {
            $refunds = $this->refundsCollection->create()
                ->addAttributeToFilter('entity_id', $id);
        } elseif ($startDate && $endDate) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));

            $refunds = $this->refundsCollection->create()
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $refunds = $this->refundsCollection->create();
        }
        $refunds->addAttributeToFilter('main_table.store_id', $this->helper->getStore()->getStoreId());
        $refunds->setOrder('created_at', $sortDir);
        $refunds->setCurPage($pageNum);
        $refunds->setPageSize($pageSize);

        if ($refunds->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($refunds as $refund) {
            $model = $this->objectManager->create('\Glew\Service\Model\Types\Refund')->parse($refund);
            if ($model) {
                $this->refunds[] = $model;
            }
        }

        return $this->refunds;
    }
}
