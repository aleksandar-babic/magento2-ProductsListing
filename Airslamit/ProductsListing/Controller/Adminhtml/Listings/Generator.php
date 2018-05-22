<?php

namespace Airslamit\ProductsListing\Controller\Adminhtml\Listings;

use \Magento\Catalog\Model\Product\Visibility;
use \Airslamit\ProductsListing\Helper\Csv;

class Generator extends \Magento\Backend\App\Action
{
    protected $_resultJsonFactory;
    protected $_csv;
    protected $_fileHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Airslamit\ProductsListing\Helper\Csv $csv,
        \Airslamit\ProductsListing\Helper\FileHelper $fileHelper
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_csv = $csv;
        $this->_fileHelper = $fileHelper;
        parent::__construct($context);
    }

    public function execute() {
        $status = [];

        try {
            $handleAllListings = $this->_fileHelper
                                       ->openFileInVar('allListings.csv', Csv::COLUMNS);
            $this->_csv->writeProducts($handleAllListings);

            $status = [
            'message' => 'CSV Files have been generated.'
            ];
        }
        catch (Exception $e) {
            $status = [
                'message' => $e->getMessage()
            ];
        }
        return $this->_resultJsonFactory
                    ->create()
                    ->setData($status);
    }
}