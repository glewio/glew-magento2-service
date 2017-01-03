<?php
namespace Glew\Service\Model\Types;

use Glew\Service\Helper\Data;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory;
use Glew\Service\Model\Types\ProductAlertFactory;

class ProductAlerts {

    public $alerts = array();
    protected $helper;
    protected $productAlertCollection;
    protected $productAlertFactory;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory $productAlertCollection
     * @param \Glew\Service\Model\Types\ProductAlertFactory $productAlertFactory
     */
    public function __construct(
        Data $helper,
        CollectionFactory $productAlertCollection,
        ProductAlertFactory $productAlertFactory
    ) {
        $this->helper = $helper;
        $this->productAlertCollection = $productAlertCollection;
        $this->productAlertFactory = $productAlertFactory;
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
            $productAlert = $this->productAlertFactory->create();
            $model = $productAlert->parse($alert);
            if ($model) {
                $this->alerts[] = $model;
            }
        }

        return $this->alerts;
    }
}
