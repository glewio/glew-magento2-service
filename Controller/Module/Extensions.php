<?php
namespace Glew\Service\Controller\Module;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Glew\Service\Model\Types\ExtensionsFactory;
use Glew\Service\Helper\Data;

class Extensions extends \Glew\Service\Controller\Module
{
    protected $resultJsonFactory;
    protected $extensionsFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Glew\Service\Model\Types\ExtensionsFactory $extensionsFactory
     * @param \Glew\Service\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ExtensionsFactory $extensionsFactory,
        Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->extensionsFactory = $extensionsFactory;
        $this->helper = $helper;
        parent::__construct($context);
        parent::initParams();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $extensions = $this->extensionsFactory->create();

        if ($this->isAuthorized() != true || $this->isEnabled() != true) {
            $result->setHttpResponseCode(\Magento\Framework\App\Response\Http::STATUS_CODE_401);
            $result->setData(['error' => 'Invalid security token or module disabled']);
            return $result;
        }

        $data = $extensions->load(
            $this->pageSize,
            $this->pageNum,
            $this->startDate,
            $this->endDate,
            $this->sortDir,
            $this->filterField,
            $this->id
        );
        return $result->setData(['extensions' => $data]);
    }
}
