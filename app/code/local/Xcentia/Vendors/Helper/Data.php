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
 * Vendors default helper
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }
    
	public function getDealer() {
		$dealer = Mage::registry('current_vendor_loaded');
		if(!$dealer) {
			$dealer = Mage::getModel('xcentia_vendors/vendor')->load(Mage::helper('customer')->getCustomer()->getEmail(), 'email');
			Mage::unregister('current_dealer_loaded');
			Mage::register('current_dealer_loaded', $dealer);
		}
		return $dealer;
	}
	
	public function createUniqueSku($title) {
		$sku = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $title));
		$prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		if($prod) {
			return $this->createUniqueSku($title.rand(1,999));
		} else {
			return $sku;
		}
	}
}
