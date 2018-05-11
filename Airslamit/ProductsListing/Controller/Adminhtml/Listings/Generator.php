<?php

namespace Airslamit\ProductsListing\Controller\Adminhtml\Listings;

use \Magento\Catalog\Model\Product\Visibility;

class Generator extends \Magento\Backend\App\Action
{
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute() {
        $status = [
            'status' => 'it works!'
        ];
        return $this->resultJsonFactory
                    ->create()
                    ->setData($status);
    }
}