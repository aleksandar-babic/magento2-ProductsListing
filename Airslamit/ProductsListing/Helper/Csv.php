<?php

namespace Airslamit\ProductsListing\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Csv extends AbstractHelper
{
    protected $_productCollectionFactory;
    protected $_categoryRepository;
    protected $_stockRegistry;
    protected $_fileHelper;
    protected $_category;
    const SHOPBYBRAND_ID = 91;
    protected static $STOREURL;

    const STATIC_DATA = [
        'condition' => 'new',
        'availability' => 'in stock',
        'gtn' => '',
        'googleProductCategory' => 'Vehicles & Parts > Vehicle Parts & Accessories >
        Motor Vehicle Parts > Motor Vehicle Suspension Parts'
    ];
    const COLUMNS = 'ID, TITLE, DESCRIPTION, LINK, CONDITION, PRICE, AVAILABILITY,
    IMAGE LINK, MPN, BRAND, GOOGLE PRODUCT CATEGORY, Custom Label 0';

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Airslamit\ProductsListing\Helper\FileHelper $fileHelper,
        \Magento\Catalog\Model\Category $category
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_stockRegistry = $stockRegistry;
        $this->_fileHelper = $fileHelper;
        $this->_category = $category;
    }

    public function getProductCollection() {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        return $collection;
    }

    private function getStockItem($id, $webSiteId) {
        $stockitem = $this->_stockRegistry->getStockItem($id, $webSiteId);
        return $stockitem;
    }

    private function getProductData($product) {
        $productData = $product->getData();
        return [
            'data' => $productData,
            'categories' => $product->getCategoryIds(),
            'url' => $product->getProductUrl(),
            'imageUrl' => $this->_fileHelper->getProductImageUrl($product),
            'price' => isset($productData['price']) ? number_format($productData['price'], 2) . ' USD' : ''
        ];
    }

    public function writeProducts($handleAll) {
        $productCollection = $this->getProductCollection();
        /* $shopByBrandSubCats = $this->_categoryRepository
                                   ->get(self::SHOPBYBRAND_ID)
                                   ->getChildrenCategories(); */
            $shopByBrandSubCats = [];

        foreach ($productCollection as $product) {
            $productArr = $this->getProductData($product);
            $productCategories = $product->getCategoryIds();

            $stop = false;
            $brandName = "airslamit";
            if ($productArr['data']['visibility'] == 4 &&
                $productArr['data']['status'] == 1) {

                for ($i=0; $i < count($productCategories) && !$stop; $i++) {
                    $currCategory = $this->_category
                                         ->load($productCategories[$i])
                                         ->getName();

                    foreach($shopByBrandSubCats as $brand) {
                        if($currCategory == $brand->getName()){
                            $brandName = $brand->getName();
                            $stop = true;
                            break;
                        }
                    }
                }

                foreach ($productCategories as $category) {
                    $categoryFriendlyName = $this->_category->load($category)->getName();
                    $categoryIsInMenu = $this->_category
                                              ->load($category)
                                              ->getIncludeInMenu() == 1;
                    $categoryIsActive = $this->_category
                                             ->load($category)
                                             ->getIsActive();

                        if (strpos($categoryFriendlyName,'/') !== false ||
                            strpos($categoryFriendlyName,'"') !== false ||
                            strpos($categoryFriendlyName,'\'') !== false) {
                            $categoryFriendlyName = str_replace('/','-',$categoryFriendlyName);
                            $categoryFriendlyName = str_replace('"','',$categoryFriendlyName);
                            $categoryFriendlyName = str_replace('\'','',$categoryFriendlyName);
                        }

                        if ($categoryIsInMenu && $categoryIsActive) {
                            $handle = $this->_fileHelper->openFileInVar($categoryFriendlyName . '.csv', self::COLUMNS);
                            $productString ="{$productArr['data']['sku']}|{$productArr['data']['name']}|{$productArr['data']['description']}|{$productArr['url']}|" . self::STATIC_DATA['condition'] . "|{$productArr['price']}|" . self::STATIC_DATA['availability'] . "|{$productArr['imageUrl']}|{$productArr['data']['sku']}|{$brandName}|" . self::STATIC_DATA['googleProductCategory'] . "|{$categoryFriendlyName}";
                            fputcsv($handleAll, explode('|', $productString));
                            fputcsv($handle, explode('|', $productString));
                            fclose($handle);
                        }
                }
            }
        }

        fclose($handleAll);
    }
}