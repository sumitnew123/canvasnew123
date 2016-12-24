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
 * Vendor helper
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Helper_Vendor extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the vendors list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getVendorsUrl()
    {
        if ($listKey = Mage::getStoreConfig('xcentia_vendors/vendor/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('xcentia_vendors/vendor/index');
    }

    public function getVendorUrl($vendor)
    {
        return Mage::getUrl('xcentia_vendors/vendor/view', array('id'=>$vendor->getId(),'vendor'=> strtolower(preg_replace('#[^0-9a-z]+#i', '-', $vendor->getTitle()) )));
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('xcentia_vendors/vendor/breadcrumbs');
    }

    public function checkVendorCreated($vendor) {
        if($vendor->getStatus()) {
            return true;
        }
        $customer = Mage::getModel("customer/customer"); 
        $customer->setWebsiteId(1); 
        $customer->loadByEmail($vendor->getEmail());
        if($customer && $customer->getVendor() > 0) {
            return true;
        }
        return false;
    }
    
    public function canBidonProject($project, $vendor = null) {
    	$product_id = $project->getProductId();
    	if(!$vendor) $vendor = Mage::helper('xcentia_vendors')->getDealer();
    	$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' );
    	$sql = "SELECT DISTINCT vs.vendor_id FROM xcentia_vendors_vendorservice vs JOIN xcentia_vendors_service_category sc ON vs.service_id = sc.service_id JOIN catalog_category_product cp ON cp.category_id = sc.category_id WHERE cp.product_id =" .$product_id . " AND vs.vendor_id=".$vendor->getId();
    	$result = $read->query( $sql);
    	if($result->rowCount() > 0) {
    		return true;
    	} else {
    		return false;
    	}
    }
}
