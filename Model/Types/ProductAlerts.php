<?php
namespace Glew\Service\Model\Types;

class ProductAlerts {

    public $alerts = array();
    protected $helper;
    protected $productAlertCollection;
    protected $objectManager;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $productAlertCollection
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $productAlertCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->productAlertCollection = $productAlertCollection;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if ($id) {
            $alerts = $this->productAlertCollection->create()
                ->addFilter('alert_stock_id', $id);
        } elseif ($startDate && $endDate) {
            $condition = "add_date BETWEEN '".date('Y-m-d 00:00:00', strtotime($startDate))."' AND '".date('Y-m-d 23:59:59', strtotime($endDate))."'";
            $alerts = $this->productAlertCollection->create()
                ->addFilter('add_date', $condition, 'string');
        } else {
            $alerts = $this->productAlertCollection->create();
        }
        $alerts->addFilter('website_id', 'website_id = ' . $this->helper->getStore()->getWebsiteId(), 'string');
        $alerts->setOrder('add_date', $sortDir);
        $alerts->setCurPage($pageNum);
        $alerts->setPageSize($pageSize);

        if ($alerts->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($alerts as $alert) {
            $model = $this->objectManager->create('\Glew\Service\Model\Types\ProductAlert')->parse($alert);
            if ($model) {
                $this->alerts[] = $model;
            }
        }

        return $this->alerts;
    }
}
