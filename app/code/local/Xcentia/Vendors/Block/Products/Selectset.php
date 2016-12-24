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
class Xcentia_Vendors_Block_Products_Selectset extends Mage_Core_Block_Template{
	/**
	 * get the current page
	 * @access public
	 * @return mixed (Wa_Dealer_Model_Page|null)
	 * @author Ultimate Module Creator
	 */
	public function getAllAttributeSets(){
		return Mage::getResourceModel('eav/entity_attribute_set_collection')
		->setEntityTypeFilter('4')  //4 = product entities
		->addFieldToFilter('attribute_set_id', array('neq' => '9'))
		->setOrder('sort_order', 'asc')
		->toOptionArray();
	}
} 