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
 * Vendor model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Vendor extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_vendors_vendor';
    const CACHE_TAG = 'xcentia_vendors_vendor';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_vendors_vendor';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'vendor';
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('xcentia_vendors/vendor');
    }

    /**
     * before save vendor
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Vendor
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the vendor details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getVendorUrl()
    {
        return Mage::getUrl('xcentia_vendors/vendor/view', array('id'=>$this->getId()));
    }

    /**
     * save vendor relation
     *
     * @access public
     * @return Xcentia_Vendors_Model_Vendor
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->saveVendorRelation($this);
        return parent::_afterSave();
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Xcentia_Vendors_Model_Vendor_Category
     * @author Ultimate Module Creator
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('xcentia_vendors/vendor_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Xcentia_Vendors_Resource_Vendor_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * Retrieve  collection
     *
     * @access public
     * @return Xcentia_Vendors_Model_Coverage_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCoveragesCollection()
    {
        if (!$this->hasData('_coverage_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('xcentia_vendors/coverage_collection')
                        ->addFieldToFilter('vendor_id', $this->getId());
                $this->setData('_coverage_collection', $collection);
            }
        }
        return $this->getData('_coverage_collection');
    }

    /**
     * Retrieve  collection
     *
     * @access public
     * @return Xcentia_Vendors_Model_Order_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedOrdersCollection()
    {
        if (!$this->hasData('_order_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('xcentia_vendors/order_collection')
                        ->addFieldToFilter('vendor_id', $this->getId());
                $this->setData('_order_collection', $collection);
            }
        }
        return $this->getData('_order_collection');
    }

    /**
     * check if comments are allowed
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllowComments()
    {
        if ($this->getData('allow_comment') == Xcentia_Vendors_Model_Adminhtml_Source_Yesnodefault::NO) {
            return false;
        }
        if ($this->getData('allow_comment') == Xcentia_Vendors_Model_Adminhtml_Source_Yesnodefault::YES) {
            return true;
        }
        return Mage::getStoreConfigFlag('xcentia_vendors/vendor/allow_comment');
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['allow_comment'] = Xcentia_Vendors_Model_Adminhtml_Source_Yesnodefault::USE_DEFAULT;
        return $values;
    }
    
}
