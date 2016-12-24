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
 * Page list block
 *
 * @category	Wa
 * @package		Wa_Dealer
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Products_List extends Mage_Core_Block_Template{
	/**
	 * initialize
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
 	public function __construct(){
		parent::__construct();
		//$website_id = Mage::app()->getStore()->getWebsiteId();
		$products = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSelect(array('price','short_description','small_image','is_featured'))
					->addAttributeToFilter('dealer_id', Mage::helper('xcentia_vendors')->getDealer()->getId())
					->addFieldToFilter('type_id','simple');
		$q = Mage::app()->getRequest()->getParam('q', '');
		if($q != '') $products->addFieldToFilter('name',array('like' => '%'.$q.'%'));
		$products->setOrder('name', 'asc');
		$this->setProducts($products);
		$this->setLoadedProductCollection($products);
	}
	/**
	 * prepare the layout
	 * @access protected
	 * @return Wa_Dealer_Block_Page_List
	 * @author Ultimate Module Creator
	 */
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'dealer.page.html.pager')
			->setCollection($this->getProducts());
		$this->setChild('pager', $pager);
		$this->getProducts()->load();
		return $this;
	}
	/**
	 * get the pager html
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}
	
	function getTreeCategories($default, $parentId = 0){		
	    $allCats = Mage::getModel('catalog/category')
	    			->getCollection()
	                ->addAttributeToSelect(array('name', 'level') )
	                ->addAttributeToFilter('parent_id',array('eq' => $parentId))
	                ->addAttributeToSort('position', 'asc'); 

	    foreach($allCats as $category)
	    {
	        if($category->getLevel() == 3) {
	        	$html .= '<optgroup label="'.$spaces.$category->getName().'">';
	        } else {
	        	$html .= '<option value="'.$category->getId().'">'.$category->getName().'</span>';
	        }
	        $subcats = $category->getChildren();
	        if($subcats != ''){
	            $html .= $this->getTreeCategories($default, $category->getId());
	        }
	    	if($category->getLevel() == 3) {
	        	$html .= '</optgroup>';
	        } else {
	        	$html .= '</option>';
	        }
	    }
	    return $html;
	}
	
	public function getVendorServices() {
		$services = Mage::getModel('xcentia_vendors/vendorservice')
					->getCollection()
					->addFieldToFilter('vendor_id', Mage::helper('xcentia_vendors')->getDealer()->getId());

		foreach($services as $service) {
			$sers[] = $service->getServiceId();
		}	
					
		$cats = Mage::getModel('xcentia_vendors/service')
				->getCollection()
				->addFieldToFilter('entity_id', array('in'=>$sers));
		return $cats;
	}
	
	public function getServices() {
		$services = Mage::getModel('xcentia_vendors/service')
						->getCollection()
						->addFieldToFilter('status', 1);
		$html = '';
		foreach($services as $service)
	    {
	    	$html .= '<option value="'.$service->getId().'">'.$service->getTitle().'</option>';
	    }
	    return $html;
	}
}