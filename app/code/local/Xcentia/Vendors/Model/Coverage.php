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
 * Coverage model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Coverage extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_vendors_coverage';
    const CACHE_TAG = 'xcentia_vendors_coverage';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_vendors_coverage';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'coverage';

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
        $this->_init('xcentia_vendors/coverage');
    }

    /**
     * before save coverage
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Coverage
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
     * save coverage relation
     *
     * @access public
     * @return Xcentia_Vendors_Model_Coverage
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Xcentia_Vendors_Model_Vendor
     * @author Ultimate Module Creator
     */
    public function getParentVendor()
    {
        if (!$this->hasData('_parent_vendor')) {
            if (!$this->getVendorId()) {
                return null;
            } else {
                $vendor = Mage::getModel('xcentia_vendors/vendor')
                    ->load($this->getVendorId());
                if ($vendor->getId()) {
                    $this->setData('_parent_vendor', $vendor);
                } else {
                    $this->setData('_parent_vendor', null);
                }
            }
        }
        return $this->getData('_parent_vendor');
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
        return $values;
    }
    
}
