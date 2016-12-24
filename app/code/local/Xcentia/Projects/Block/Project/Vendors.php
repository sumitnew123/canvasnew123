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
 * Project view block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Project_Vendors extends Mage_Core_Block_Template
{
    /**
     * get the current project
     *
     * @access public
     * @return mixed (Xcentia_Projects_Model_Project|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentProject()
    {
        return Mage::registry('current_project');
    }
    
    public function getVendors($project) {
    	$product_id = $project->getProductId();
    	$product = Mage::getModel('catalog/product')->load($product_id);
    	$categories = $product->getCategoryIds();
    	$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql        = "SELECT DISTINCT vs.vendor_id from xcentia_vendors_vendorservice vs INNER JOIN xcentia_vendors_service_category sc ON vs.service_id=sc.service_id WHERE sc.category_id IN (".implode(",",$categories).")";
		$rows       = $connection->fetchAll($sql);
		foreach($rows as $row) {
			$vendors[] = $row['vendor_id'];
		}
    	
    	$vendors = Mage::getResourceModel('xcentia_vendors/vendor_collection')
                         ->addFieldToFilter('status', 1)
                         ->addFieldToFilter('entity_id', array('in' => $vendors));
        $vendors->setOrder('title', 'asc');
       	return $vendors;
    }
}
