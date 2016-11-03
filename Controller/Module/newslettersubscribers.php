<?php
namespace Glew\Service\Controller\Module;

class Newslettersubscribers extends \Glew\Service\Controller\Module {

    protected $resultJsonFactory;
    protected $subscribers;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Glew\Service\Model\Types\Subscribers $subscribers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Glew\Service\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Glew\Service\Model\Types\Subscribers $subscribers,
        //\Magento\Framework\ObjectManagerInterface $objectManager,
        \Glew\Service\Helper\Data $helper
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->subscribers = $subscribers;
        $this->helper = $helper;
        //$this->objectManager = $objectManager;
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

        $data = $this->subscribers->load(
            $this->pageSize,
            $this->pageNum,
            $this->sortDir,
            $this->filterField,
            $this->id
        );
        return $result->setData($data);
    }
}
