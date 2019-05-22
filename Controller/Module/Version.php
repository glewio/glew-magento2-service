<?php
namespace Glew\Service\Controller\Module;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\ProductMetadataInterfaceFactory;
use Glew\Service\Helper\Data;

class Version extends \Glew\Service\Controller\Module
{
    protected $resultJsonFactory;
    protected $productMetadataInterfaceFactory;
    protected $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\ProductMetadataInterfaceFactory $productMetadataInterfaceFactory
     * @param \Glew\Service\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductMetadataInterfaceFactory $productMetadataInterfaceFactory,
        Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productMetadataInterfaceFactory = $productMetadataInterfaceFactory;
        $this->helper = $helper;
        parent::__construct($context);
        parent::initParams();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $productMetadata = $this->productMetadataInterfaceFactory->create();

        $result = $this->resultJsonFactory->create();
        $data = new \stdClass();
        $data->glewPluginVersion = (string) $this->helper->getVersion();
        $data->magentoVersion = (string) $productMetadata->getVersion();
        $data->magentoEdition = (string) $productMetadata->getEdition();
        $data->phpVersion = PHP_VERSION;
        $data->moduleEnabled = $this->helper->getConfig()['enabled'];
        $data->apiVersion = '2.0';
        $data->memoryLimit = @ini_get('memory_limit');
        $data->maxExecutionTime = @ini_get('max_execution_time');
        return $result->setData($data);
    }
}
