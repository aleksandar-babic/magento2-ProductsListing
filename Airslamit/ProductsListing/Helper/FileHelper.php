<?php

namespace Airslamit\ProductsListing\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class FileHelper extends AbstractHelper {

    protected $_dir;
    protected $_mageRoot;
    protected $_mageVar;
    protected $_storeManager;

    public function __construct
    (
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_dir = $dir;
        $this->_mageRoot = $this->_dir->getRoot();
        $this->_mageVar = $this->_dir->getPath('var');
        $this->_storeManager = $storeManager;
    }

    private function createAndWriteHeaders($fileName, $headers) {
        $handle = fopen($fileName, 'w');
        fputcsv($handle, explode(',', $headers));
        return $handle;
    }

    public function openFileInVar($fileName, $headers = '', $subDir = 'products-listing') {
        if ($subDir === '') return;

        $filePath = "$this->_mageVar/$subDir/$fileName";
        return file_exists($filePath) ? fopen($filePath, 'a') :
                                        $this->createAndWriteHeaders($filePath, $headers);
    }

    public function getProductImageUrl($product) {
        $storeUrl = $this->_storeManager->getStore()->getBaseUrl();
        $productImage = $product->getImage();
        return "$storeUrl/pub/media/catalog/product/$productImage";
    }
}