<?php
namespace Glew\Service\Model\Types;
class Products {
    public $products = array();
    protected $helper;
    protected $productFactory;
    protected $objectManager;
    private $pageNum;
    private $productAttributes = array();
    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->objectManager = $objectManager;
    }
    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;
        $this->_getProductAttribtues();
        if( $id ) {
            $products = $this->productFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', $id);
        } elseif ($startDate && $endDate) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));
            $products = $this->productFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $products = $this->productFactory->create()
                ->addAttributeToSelect('*');
        }

        $products->setStore($this->helper->getStore());
        $products->setOrder('updated_at', $sortDir);
        $products->setCurPage($pageNum);
        $products->setPageSize($pageSize);
        if ($products->getLastPageNumber() < $pageNum) {
            return $this;
        }
        foreach ($products as $product) {
            $productId = $product->getId();
            $model = $this->objectManager->create('\Glew\Service\Model\Types\Product')->parse($productId, $this->productAttributes);
            if ($model) {
                $model->cross_sell_products = $this->_getCrossSellProducts($product);
                $model->up_sell_products = $this->_getUpSellProducts($product);
                $model->related_products = $this->_getRelatedProducts($product);
                $this->products[] = $model;
            }
        }
        return $this->products;
    }
    protected function _getProductAttribtues()
    {
        if (!$this->productAttributes) {
            $attributes = $this->objectManager->get('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
                ->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, 4)
                ->load()
                ->getItems();
            foreach ($attributes as $attribute) {
                if (!$attribute) {
                    continue;
                }
                $this->productAttributes[$attribute->getData('attribute_code')] = $attribute->usesSource();
            }
        }
    }
    protected function _getCrossSellProducts($product)
    {
        $productArray = array();
        $collection = $product->getCrossSellProductCollection();
        if ($collection) {
            foreach ($collection as $item) {
                $productArray[] = $item->getId();
            }
        }
        return $productArray;
    }
    protected function _getUpSellProducts($product)
    {
        $productArray = array();
        $collection = $product->getUpSellProductCollection();
        if ($collection) {
            foreach ($collection as $item) {
                $productArray[] = $item->getId();
            }
        }
        return $productArray;
    }
    protected function _getRelatedProducts($product)
    {
        $productArray = array();
        $collection = $product->getRelatedProductCollection();
        if ($collection) {
            foreach ($collection as $item) {
                $productArray[] = $item->getId();
            }
        }
        return $productArray;
    }
}
