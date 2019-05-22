<?php
namespace Glew\Service\Model\Types;

use Glew\Service\Helper\Data;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use Glew\Service\Model\Types\SubscriberFactory;

class Subscribers
{
    public $subscribers = array();
    private $pageNum;
    protected $helper;
    protected $collectionFactory;
    protected $subscriberFactory;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $collectionFactory
     * @param \Glew\Service\Model\Types\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Data $helper,
        CollectionFactory $collectionFactory,
        SubscriberFactory $subscriberFactory
    ) {
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->subscriberFactory = $subscriberFactory;
    }

    public function load($pageSize, $pageNum, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if ($id) {
            $subscribers = $this->collectionFactory->create()
                ->addFieldToFilter('main_table.subscriber_id', $id);
        } else {
            $subscribers = $this->collectionFactory->create();
        }
        $subscribers->addFilter('store_id', 'store_id = '.$this->helper->getStore()->getStoreId(), 'string');
        $subscribers->setOrder('subscriber_id', $sortDir);
        $subscribers->setCurPage($pageNum);
        $subscribers->setPageSize($pageSize);

        if ($subscribers->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($subscribers as $subscriber) {
            $model = $this->subscriberFactory->create();
            $glewSubscriber = $model->parse($subscriber);
            if ($glewSubscriber) {
                $this->subscribers[] = $glewSubscriber;
            }
        }

        return $this->subscribers;
    }
}
