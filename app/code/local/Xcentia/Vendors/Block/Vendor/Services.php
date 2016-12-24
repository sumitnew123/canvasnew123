<?php
/**
 * Xcentia_Vendors extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Vendors
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Vendor view block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Vendor_Services extends Mage_Customer_Block_Form_Register
{
    /**
     * get the current vendor
     *
     * @access public
     * @return mixed (Xcentia_Vendors_Model_Vendor|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentVendor()
    {
        return Mage::registry('current_vendor');
    }
    
    public function getServices() {
    	$vendor = $this->getCurrentVendor();
    	$services = Mage::getModel('xcentia_vendors/vendorservice')
					->getCollection()
					->addFieldToFilter('vendor_id', $vendor->getId());

		foreach($services as $service) {
			$sers[] = $service->getServiceId();
		}	
					
		$cats = Mage::getModel('xcentia_vendors/service')
				->getCollection()
				->addFieldToFilter('entity_id', array('in'=>$sers));
		return $cats;
    }
    
}
