<?php
namespace Glew\Service\Controller\Module;

class Refunditems extends \Glew\Service\Controller\Module {

    protected $resultJsonFactory;
    protected $refundItems;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Glew\Service\Model\Types\RefundItems $refundItems
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Glew\Service\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Glew\Service\Model\Types\RefundItems $refundItems,
        \Glew\Service\Helper\Data $helper
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->refundItems = $refundItems;
        $this->helper = $helper;
        $this->objectManager = $context->getObjectManager();
        parent::__construct($context);
        parent::initParams();

    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if($this->isAuthorized() != true || $this->isEnabled() != true) {
            $result->setHttpResponseCode(\Magento\Framework\App\Response\Http::STATUS_CODE_401);
            $result->setData(['error' => 'Invalid security token or module disabled']);
            return $result;
        }

        $data = $this->refundItems->load(
            $this->pageSize,
            $this->pageNum,
            $this->startDate,
            $this->endDate,
            $this->sortDir,
            $this->filterField,
            $this->id
        );

        return $result->setData($data);
    }
}
