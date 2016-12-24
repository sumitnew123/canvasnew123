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
 * Adminhtml observer
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the vendor tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Xcentia_Vendors_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addCategoryVendorBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'xcentia_vendors/adminhtml_catalog_category_tab_vendor',
            'category.vendor.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.vendor.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.vendor.grid',
            'getSelectedVendors',
            'vendors',
            'category_vendors'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'vendor',
            array(
                'label'   => Mage::helper('xcentia_vendors')->__('Vendors'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save vendor - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Xcentia_Vendors_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveCategoryVendorData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('vendors', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $vendorCategory = Mage::getResourceSingleton('xcentia_vendors/vendor_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}
