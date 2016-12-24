<?php
/**
 * Xcentia_Projects extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Projects
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Project front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_CollectionController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() {
		$products = array();
		$page = $this->getRequest()->getParam('page', 1);
		$category = $this->getRequest()->getParam('category', 27);
		$vendor = $this->getRequest()->getParam('vendor', 0);
		
		$category = Mage::getModel('catalog/category')->load($category);
    	$productcollection = $category->getProductCollection()->addCategoryFilter($category)
                         ->addAttributeToFilter('type_id', 'simple')
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('status', 1)
                         ->addFinalPrice()
                         ->addMinimalPrice()
                         ->setPageSize(10)
            			 ->setCurPage($page);
                         ;
        if($vendor > 0) {
        	$productcollection->addAttributeToFilter('dealer_id', $vendor);
        }
        foreach($productcollection as $prod) {
        	$products[] = (object)array('id' => $prod->getId(),
        							'name' => $prod->getName(),
        							'price' => $prod->getPrice(),
        							'image' => (String)Mage::helper('catalog/image')->init($prod, 'image')->keepframe(false)->resize(600),
        							);
        }
        if(($page-1)*10 < $productcollection->getSize()) {
        	$response = array('total' => $productcollection->getSize(), 'page' => (int)$page, 'items' => $products);
        } else {
        	$response = array('total' => $productcollection->getSize(), 'page' => (int)$page, 'items' => array());
        }
        
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function viewAction() {
		$productId  = (int) $this->getRequest()->getParam('id');
		$product = Mage::helper('catalog/product')->initProduct($productId);
		$dealer = Mage::getModel('xcentia_vendors/vendor')->load($product->getDealerId());
		
		$prodinfo = new stdClass();
		$prodinfo->name = $product->getName();
		$prodinfo->image = (String)Mage::helper('catalog/image')->init($product, 'image');
		$prodinfo->vendor = $dealer->getTitle();
		$prodinfo->description = strip_tags($product->getDescription());
		$prodinfo->price = $product->getPrice();
		$prodinfo->id = $product->getId();
				
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($prodinfo));

	}
	
	public function categoriesAction() {
		$rootcategory = Mage::getModel('catalog/category')->load(27);
    	$categoryCollection= $rootcategory->getChildrenCategories();
		$categoryCollection->addIsActiveFilter();
		$categoryCollection->load();
		$categories = array();
		$categories[27]['name'] = 'All';
		$categories[27]['id'] = 27;
		foreach ($categoryCollection as $category) {
			$categories[$category->getId()]['name'] = $category->getName();
			$categories[$category->getId()]['id'] = $category->getId();
			$subcats = Mage::getResourceModel('catalog/category_collection')
		                     ->addAttributeToSelect('name')
		                     ->addAttributeToFilter('entity_id', explode(',', $category->getChildren()))
		                     ->addIsActiveFilter();
			foreach($subcats as $subcat) {
				$categories[$category->getId()]['subcats'][$subcat->getId()]['name'] = $subcat->getName();
				$categories[$category->getId()]['subcats'][$subcat->getId()]['id'] = $subcat->getId();
            }                 
	
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($categories));
	}
}