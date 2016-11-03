<?php
namespace Glew\Service\Model\Types;

class Stores {

    public $stores = array();
    protected $helper;
    protected $objectManager;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;

        $stores = $this->helper->getStores();
        $stores = $this->helper->paginate($stores, $pageNum, $pageSize);
        foreach($stores as $store) {
            $model = $this->objectManager->create('\Glew\Service\Model\Types\Store')->parse($store);
            if ($model) {
                $this->stores[] = $model;
            }
        }

        return $this->stores;
    }
}
