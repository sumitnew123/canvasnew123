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
 * Service category model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Service_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_init('xcentia_vendors/service_category');
    }

    /**
     * Save data for service-category relation
     *
     * @access public
     * @param  Xcentia_Vendors_Model_Service $service
     * @return Xcentia_Vendors_Model_Service_Category
     * @author Ultimate Module Creator
     */
    public function saveServiceRelation($service)
    {
        $data = $service->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveServiceRelation($service, $data);
        }
        return $this;
    }

    /**
     * get categories for service
     *
     * @access public
     * @param Xcentia_Vendors_Model_Service $service
     * @return Xcentia_Vendors_Model_Resource_Service_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection($service)
    {
        $collection = Mage::getResourceModel('xcentia_vendors/service_category_collection')
            ->addServiceFilter($service);
        return $collection;
    }
}
