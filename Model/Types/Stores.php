<?php
namespace Glew\Service\Model\Types;

use Glew\Service\Helper\Data;
use Glew\Service\Model\Types\StoreFactory;

class Stores
{
    public $stores = array();
    protected $helper;
    protected $storeFactory;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Glew\Service\Model\Types\StoreFactory $storeFactory
     */
    public function __construct(
        Data $helper,
        StoreFactory $storeFactory
    ) {
        $this->helper = $helper;
        $this->storeFactory = $storeFactory;
    }

    public function load($pageSize, $pageNum)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;

        $stores = $this->helper->getStores();
        $stores = $this->helper->paginate($stores, $pageNum, $pageSize);

        foreach ($stores as $store) {
            $glewStore = $this->storeFactory->create();
            $storeModel = $glewStore->parse($store);
            if ($storeModel) {
                $this->stores[] = $storeModel;
            }
        }

        return $this->stores;
    }
}
