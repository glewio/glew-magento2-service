<?php
namespace Glew\Service\Model\Types;

class AbandonedCarts
{
    public $carts = array();
    protected $helper;
    protected $cartCollection;
    protected $objectManager;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Reports\Model\ResourceModel\Quote\Collection $cartCollection
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Reports\Model\ResourceModel\Quote\Collection $cartCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->cartCollection = $cartCollection;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if($id) {
            $collection = $this->cartCollection
                ->addFieldToFilter('main_table.entity_id', $id);
        } elseif ($startDate && $endDate) {
            $filter = array(
                'datetime' => 1,
                'locale' => 'en_US',
                'from' => new Zend_Date(strtotime($startDate), Zend_Date::TIMESTAMP),
                'to' => new Zend_Date(strtotime($endDate . ' + 1 day -1 second'), Zend_Date::TIMESTAMP),
            );

            $collection = $this->cartCollection
                ->addFieldToFilter('main_table.'.$filterBy, $filter);
        } else {
            $collection = $this->cartCollection;
        }
        $collection->addFieldToFilter('main_table.store_id', $this->helper->getStore()->getStoreId());
        $collection->prepareForAbandonedReport(array($this->helper->getStore()->getWebsiteId()));
        $collection->setOrder('created_at', $sortDir);
        $collection->setCurPage($pageNum);
        $collection->setPageSize($pageSize);
        $collection->load();

        if ($collection->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($collection as $cart) {
            if ($cart) {
                $model = $this->objectManager->create('\Glew\Service\Model\Types\AbandonedCart')->parse($cart);
                if ($model) {
                    $this->carts[] = $model;
                }
            }
        }

        return $this->carts;
    }
}
