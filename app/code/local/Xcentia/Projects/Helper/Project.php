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
 * Project helper
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Helper_Project extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the projects list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getProjectsUrl()
    {
        if ($listKey = Mage::getStoreConfig('xcentia_projects/project/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('xcentia_projects/project/index');
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
        return Mage::getStoreConfigFlag('xcentia_projects/project/breadcrumbs');
    }
    
    public function hasAlreadyBidByVendor($project, $vendor = null) {
    	if(!$vendor) $vendor = Mage::helper('xcentia_vendors')->getDealer();
    	$read = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' );
    	$sql = "SELECT date FROM xcentia_projects_bid WHERE project_id =" .$project->getId() . " AND vendor_id=".$vendor->getId();
    	$result = $read->query( $sql);
    	if($result->rowCount() > 0) {
    		return true;
    	} else {
    		return false;
    	}
    }
}
