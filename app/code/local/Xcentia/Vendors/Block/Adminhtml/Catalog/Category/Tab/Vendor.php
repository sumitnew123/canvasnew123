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
 * Vendor tab on category edit form
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Catalog_Category_Tab_Vendor extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_vendor');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_vendors'=>1));
        }
    }

    /**
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     * @author Ultimate Module Creator
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * prepare the collection
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Catalog_Category_Tab_Vendor
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('xcentia_vendors/vendor_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('xcentia_vendors/vendor_category')),
            'related.vendor_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare the columns
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Catalog_Category_Tab_Vendor
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_vendors',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_vendors',
                'values' => $this->_getSelectedVendors(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('xcentia_vendors')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => Mage::helper('xcentia_vendors')->__('Title'),
                'align'  => 'left',
                'index'  => 'title',
                'renderer' => 'xcentia_vendors/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/vendors_vendor/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('xcentia_vendors')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected vendors
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getSelectedVendors()
    {
        $vendors = $this->getCategoryVendors();
        if (!is_array($vendors)) {
            $vendors = array_keys($this->getSelectedVendors());
        }
        return $vendors;
    }

    /**
     * Retrieve selected vendors
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedVendors()
    {
        $vendors = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('xcentia_vendors/category')->getSelectedVendors(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $vendor) {
            $vendors[$vendor->getId()] = array('position' => $vendor->getPosition());
        }
        return $vendors;
    }

    /**
     * get row url
     *
     * @access public
     * @param Xcentia_Vendors_Model_Vendor
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/vendors_vendor_catalog_category/vendorsgrid',
            array(
                'id'=>$this->getCategory()->getId()
            )
        );
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Xcentia_Vendors_Block_Adminhtml_Catalog_Category_Tab_Vendor
     * @author Ultimate Module Creator
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_vendors') {
            $vendorIds = $this->_getSelectedVendors();
            if (empty($vendorIds)) {
                $vendorIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$vendorIds));
            } else {
                if ($vendorIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$vendorIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
