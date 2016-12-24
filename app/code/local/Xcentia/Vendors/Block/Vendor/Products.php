<?php 
/**
 * Webardent_Dealer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Webardent
 * @package		Webardent_Dealer
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Dealer list block
 *
 * @category	Webardent
 * @package		Webardent_Dealer
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Dealer_Products extends Mage_Catalog_Block_Product_Abstract {
	/**
	 * initialize
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
 	public function getDealerProducts(){
 		$dealer = $this->getDealer();
		$products = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSelect(array('name','price', 'small_image', 'thumbnail', 'image'))
					->addAttributeToFilter('dealer_id', $dealer->getId())
					->addFieldToFilter('type_id','simple')
					->setOrder('created_at', 'desc')
					->setPageSize(20)
            		->setCurPage(1)
            		->load();

		return $products;
 	}
 	
	public function getDealer(){
		return Mage::registry('current_dealer_dealer');
	}
}