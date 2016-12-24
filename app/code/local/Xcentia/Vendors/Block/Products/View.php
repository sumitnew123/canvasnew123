<?php 
/**
 * Wa_Dealer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Wa
 * @package		Wa_Dealer
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Page view block
 *
 * @category	Wa
 * @package		Wa_Dealer
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Products_View extends Mage_Core_Block_Template{
	/**
	 * get the current page
	 * @access public
	 * @return mixed (Wa_Dealer_Model_Page|null)
	 * @author Ultimate Module Creator
	 */
	public function getCurrentPage(){
		return Mage::registry('current_dealer_product');
	}
	
	public function getCategories() {
		$store_id = Mage::helper('Dealer')->getShopStoreId();
		$root = Mage::app()->getStore($store_id)->getRootCategoryId();
		return Mage::getModel('catalog/category')->setStoreId($store_id)->load($root)->getChildrenCategories();
	}
	
	public function getTaxClasses() {
		return Mage::getModel('tax/class')->getCollection()->addFieldToFilter('class_type', 'PRODUCT');
	}
	
	function getTreeCategories($default, $parentId = 0){
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql        = "SELECT DISTINCT sc.category_id from xcentia_vendors_vendorservice vs INNER JOIN xcentia_vendors_service_category sc ON vs.service_id=sc.service_id WHERE vs.vendor_id=".Mage::helper('xcentia_vendors')->getDealer()->getId();
		$rows       = $connection->fetchAll($sql);
		foreach($rows as $row) {
			$cats[] = $row['category_id'];
		}
		
	    $allCats = Mage::getModel('catalog/category')
	    			->getCollection()
	                ->addAttributeToSelect(array('name', 'level') )
	                ->addAttributeToFilter('parent_id',array('eq' => $parentId))
	                ->addAttributeToFilter('is_active', 1)
	                ->addAttributeToFilter('entity_id', array('in'=>$cats))
	                ->addAttributeToSort('position', 'asc'); 
	
	    foreach($allCats as $category)
	    {
	    	$spaces = ( $category->getLevel() > 1 ) ? str_repeat("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp", $category->getLevel()-2): '';
	        $html .= '<option value="'.$category->getId().'" '.(( in_array($category->getId(), $default ) ) ? 'selected="selected"' : '').'>'.$spaces.$category->getName().'</span>';
	        $subcats = $category->getChildren();
	        if($subcats != ''){
	            $html .= $this->getTreeCategories($default, $category->getId());
	        }
	    }
	    return $html;
	}
} 