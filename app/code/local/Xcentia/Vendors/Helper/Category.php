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
 * Category helper
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Helper_Category extends Xcentia_Vendors_Helper_Data
{

    /**
     * get the selected vendors for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedVendors(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedVendors()) {
            $vendors = array();
            foreach ($this->getSelectedVendorsCollection($category) as $vendor) {
                $vendors[] = $vendor;
            }
            $category->setSelectedVendors($vendors);
        }
        return $category->getData('selected_vendors');
    }

    /**
     * get vendor collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Xcentia_Vendors_Model_Resource_Vendor_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedVendorsCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('xcentia_vendors/vendor_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
