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
 * Vendorservice model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Vendorservice extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_vendors_vendorservice';
    const CACHE_TAG = 'xcentia_vendors_vendorservice';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_vendors_vendorservice';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'vendorservice';

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
        $this->_init('xcentia_vendors/vendorservice');
    }

    /**
     * before save vendorservice
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Vendorservice
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
     * save vendorservice relation
     *
     * @access public
     * @return Xcentia_Vendors_Model_Vendorservice
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
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
