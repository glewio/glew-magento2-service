<?php
namespace Glew\Service\Model\Types;

class Categories {

    public $categories = array();
    protected $helper;
    protected $categoryCollection;
    protected $objectManager;
    private $pageNum;

    /**
     * @param \Glew\Service\Helper\Data $helper
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollection
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Glew\Service\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->categoryCollection = $categoryCollection;
        $this->objectManager = $objectManager;
    }

    public function load($pageSize, $pageNum, $startDate = null, $endDate = null, $sortDir, $filterBy, $id)
    {
        $config = $this->helper->getConfig();
        $this->pageNum = $pageNum;

        if ($id) {
            $categories = $this->categoryCollection->create()
                ->addAttributeToFilter('entity_id', $id);
        } elseif ($startDate && $endDate) {
            $from = date('Y-m-d 00:00:00', strtotime($startDate));
            $to = date('Y-m-d 23:59:59', strtotime($endDate));

            $categories = $this->categoryCollection->create()
                ->addAttributeToFilter($filterBy, array('from' => $from, 'to' => $to));
        } else {
            $categories = $this->categoryCollection->create();
        }
        //$categories->addAttributeToFilter('path', array('like' => '1/'.'%'));
        $categories->setOrder('created_at', $sortDir);
        $categories->setCurPage($pageNum);
        $categories->setPageSize($pageSize);

        if ($categories->getLastPageNumber() < $pageNum) {
            return $this;
        }

        foreach ($categories as $cat) {
            $category = $this->objectManager->create('\Glew\Service\Model\Types\Category');
            $model = $category->parse($cat);
            if ($model) {
                $this->categories[] = $model;
            }
        }

        return $this->categories;
    }
}
