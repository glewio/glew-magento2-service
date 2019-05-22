<?php
namespace Glew\Service\Model\Types;

use Glew\Service\Helper\Data;
use Magento\Reports\Model\ResourceModel\Quote\Collection;
use Glew\Service\Model\Types\AbandonedCartFactory;

class AbandonedCarts
{
    public $carts = array();
    protected $helper;
    protected $cartCollection;
    protected $abandonedCartFactory;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Reports\Model\ResourceModel\Quote\Collection $cartCollection
     * @param \Glew\Service\Model\Types\AbandonedCartFactory
     */
    public function __construct(
        Data $helper,
        Collection $cartCollection,
        AbandonedCartFactory $abandonedCartFactory
    ) {
        $this->helper = $helper;
        $this->cartCollection = $cartCollection;
        $this->abandonedCartFactory = $abandonedCartFactory;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if ($id) {
            $collection = $this->cartCollection
                ->addFieldToFilter('main_table.entity_id', $id);
        } elseif ($startDate && $endDate) {
            $filter = array(
                'datetime' => 1,
                'locale' => 'en_US',
                'from' => date('Y-m-d 00:00:00', strtotime($startDate)),
                'to' => date('Y-m-d 23:59:59', strtotime($endDate)),
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
                $abandonedCarts = $this->abandonedCartFactory->create();
                $model = $abandonedCarts->parse($cart);
                if ($model) {
                    $this->carts[] = $model;
                }
            }
        }

        return $this->carts;
    }
}
