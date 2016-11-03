<?php
namespace Glew\Service\Model\Types;

class Subscribers {

    public $subscribers = array();
    private $pageNum;
    protected $helper;
    protected $subscriberFactory;
    protected $objectManager;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->subscriberFactory = $subscriberFactory;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if ($id) {
            $subscribers = $this->subscriberFactory->create()
                ->addFieldToFilter('main_table.subscriber_id', $id);
        } else {
            $subscribers = $this->subscriberFactory->create();
        }
        $subscribers->addFilter('store_id', 'store_id = '.$this->helper->getStore()->getStoreId(), 'string');
        $subscribers->setOrder('subscriber_id', $sortDir);
        $subscribers->setCurPage($pageNum);
        $subscribers->setPageSize($pageSize);

        if ($subscribers->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($subscribers as $subscriber) {
            $model = $this->objectManager->create('\Glew\Service\Model\Types\Subscriber')->parse($subscriber);
            if ($model) {
                $this->subscribers[] = $model;
            }
        }

        return $this->subscribers;
    }
}
