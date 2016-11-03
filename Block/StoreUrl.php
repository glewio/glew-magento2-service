<?php
namespace Glew\Service\Block;
use Magento\Framework\Data\Form\Element\AbstractElement;

class StoreUrl extends \Magento\Config\Block\System\Config\Form\Field {

    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element) {
        $html = $element->getElementHtml();
        $html .= $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, true);
        return $html;
    }
}
