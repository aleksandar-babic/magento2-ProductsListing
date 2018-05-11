<?php

namespace Airslamit\ProductsListing\Block\System\Config;
class Button extends \Magento\Config\Block\System\Config\Form\Field
{
     protected $_template = 'Airslamit_ProductsListing::button.phtml';

     public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
     ) {
        parent::__construct($context, $data);
     }

     public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
     }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        return $this->_toHtml();
    }

    public function getAjaxUrl() {
        return $this->getUrl('generate/listings/generator');
    }

    public function getButtonHtml() {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'generate',
                'label' => __('Generate CSV Listings'),
            ]
        );

        return $button->toHtml();
    }
}