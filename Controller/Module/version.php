<?php
namespace Glew\Service\Controller\Module;

class Version extends \Glew\Service\Controller\Module {

    protected $resultJsonFactory;
    protected $helper = null;
    protected $config = null;
    protected $objectManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Glew\Service\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Glew\Service\Helper\Data $helper
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
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
        /** @var \Magento\Framework\Controller\Result\Json $result */
		$result = $this->resultJsonFactory->create();
        $data = new \stdClass();
        $data->glewPluginVersion = (string) $this->helper->getVersion();
        $data->magentoVersion = (string) $this->objectManager->get('\Magento\Framework\App\ProductMetadata')->getVersion();
        $data->phpVersion = (string) phpversion();
        $data->moduleEnabled = $this->helper->getConfig()['enabled'];
        $data->apiVersion = "2.0";
        return $result->setData($data);
    }
}
