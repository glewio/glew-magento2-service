<?php
namespace Glew\Service\Model\Types;

class Customers {

    public $customers = array();
    private $pageNum;
    protected $helper;
    protected $customerFactory;
    protected $mageCustomer;
    protected $objectManager;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param \Magento\Customer\Model\Customer $mageCustomer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Customer\Model\Customer $mageCustomer,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->mageCustomer = $mageCustomer;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id) {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if ($id) {
            $collection = $this->customerFactory->create()
                ->addAttributeToFilter('entity_id', $id);
        } elseif ($startDate && $endDate) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));

            $collection = $this->customerFactory->create()
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $collection = $this->customerFactory->create();
        }
        $collection->addAttributeToFilter('store_id', $this->helper->getStore()->getStoreId());
        $collection->setOrder('created_at', $sortDir);
        $collection->setCurPage($pageNum);
        $collection->setPageSize($pageSize);

        if ($collection->getLastPageNumber() < $pageNum) {
            return $this;
        }
        foreach ($collection as $customer) {
            $mageCustomer = $this->objectManager->create('\Magento\Customer\Model\Customer');
            $customer = $mageCustomer->load($customer->getId());
            if ($customer && $customer->getId()) {
                $customerParser = $this->objectManager->create('\Glew\Service\Model\Types\Customer');
                $model = $customerParser->parse($customer);
                if ($model) {
                    $this->customers[] = $model;
                }
            }
        }

        return $this->customers;
    }
}
