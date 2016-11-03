<?php
namespace Glew\Service\Model\Types;

class Inventory {

    public $inventory = array();
    private $pageNum;
    protected $helper;
    protected $productFactory;
    protected $objectManager;
    protected $catalogConfig;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
    ) {
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->objectManager = $objectManager;
        $this->catalogConfig = $catalogConfig;
    }

    public function load($pageSize, $pageNum, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        if($id) {
            $inventory = $this->productFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', $id);
        } else {
            $inventory = $this->productFactory->create()
                ->addAttributeToSelect('*');
        }
        $inventory->setStore($this->helper->getStore());
        $inventory->setOrder('entity_id', $sortDir);
        $inventory->setCurPage($pageNum);
        $inventory->setPageSize($pageSize);

        if ($inventory->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($inventory as $product) {
            $productParser = $this->objectManager->create('\Glew\Service\Model\Types\InventoryItem');
            $model = $productParser->parse($product);
            if ($model) {
                $this->inventory[] = $model;
            }
        }

        return $this->inventory;
    }
}
