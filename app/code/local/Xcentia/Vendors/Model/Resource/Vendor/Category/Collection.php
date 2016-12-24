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
 * Vendor - Category relation resource model collection
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Resource_Vendor_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Xcentia_Vendors_Model_Resource_Vendor_Category_Collection
     * @author Ultimate Module Creator
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('xcentia_vendors/vendor_category')),
                'related.category_id = e.entity_id',
                array('position', 'rel_id')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add vendor filter
     *
     * @access public
     * @param Xcentia_Vendors_Model_Vendor | int $vendor
     * @return Xcentia_Vendors_Model_Resource_Vendor_Category_Collection
     * @author Ultimate Module Creator
     */
    public function addVendorFilter($vendor)
    {
        if ($vendor instanceof Xcentia_Vendors_Model_Vendor) {
            $vendor = $vendor->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.vendor_id = ?', $vendor);
        return $this;
    }
}
