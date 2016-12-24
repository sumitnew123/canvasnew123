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
 * Vendor abstract REST API handler model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
abstract class Xcentia_Vendors_Model_Api2_Vendor_Rest extends Xcentia_Vendors_Model_Api2_Vendor
{
    /**
     * current vendor
     */
    protected $_vendor;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Ultimate Module Creator
     */
    protected function _retrieve() {
        $vendor = $this->_getVendor();
        $this->_prepareVendorForResponse($vendor);
        return $vendor->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('xcentia_vendors/vendor_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addFieldToFilter('status', array('eq' => 1));
        $this->_applyCollectionModifiers($collection);
        $vendors = $collection->load();
        $vendors->walk('afterLoad');
        foreach ($vendors as $vendor) {
            $this->_setVendor($vendor);
            $this->_prepareVendorForResponse($vendor);
        }
        $vendorsArray = $vendors->toArray();
        $vendorsArray = $vendorsArray['items'];

        return $vendorsArray;
    }

    /**
     * prepare vendor for response
     *
     * @access protected
     * @param Xcentia_Vendors_Model_Vendor $vendor
     * @author Ultimate Module Creator
     */
    protected function _prepareVendorForResponse(Xcentia_Vendors_Model_Vendor $vendor) {
        $vendorData = $vendor->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $vendorData['url'] = $vendor->getVendorUrl();
        }
    }

    /**
     * create vendor
     *
     * @access protected
     * @param array $data
     * @return string|void
     * @author Ultimate Module Creator
     */
    protected function _create(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * update vendor
     *
     * @access protected
     * @param array $data
     * @author Ultimate Module Creator
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete vendor
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current vendor
     *
     * @access protected
     * @param Xcentia_Vendors_Model_Vendor $vendor
     * @author Ultimate Module Creator
     */
    protected function _setVendor(Xcentia_Vendors_Model_Vendor $vendor) {
        $this->_vendor = $vendor;
    }

    /**
     * get current vendor
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Vendor
     * @author Ultimate Module Creator
     */
    protected function _getVendor() {
        if (is_null($this->_vendor)) {
            $vendorId = $this->getRequest()->getParam('id');
            $vendor = Mage::getModel('xcentia_vendors/vendor');
            $vendor->load($vendorId);
            if (!($vendor->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_vendor = $vendor;
        }
        return $this->_vendor;
    }
}
