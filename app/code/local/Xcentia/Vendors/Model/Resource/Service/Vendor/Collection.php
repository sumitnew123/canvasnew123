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
 * Service - Category relation resource model collection
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Resource_Service_Vendor_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
	
	protected function _construct()
    {
        parent::_construct();
        $this->_init('xcentia_vendors/service_vendor');
    }
    
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
     * @return Xcentia_Vendors_Model_Resource_Service_Category_Collection
     * @author Ultimate Module Creator
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('xcentia_vendors/service_vendor')),
                'related.vendor_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add service filter
     *
     * @access public
     * @param Xcentia_Vendors_Model_Service | int $service
     * @return Xcentia_Vendors_Model_Resource_Service_Category_Collection
     * @author Ultimate Module Creator
     */
    public function addServiceFilter($service)
    {
        if ($service instanceof Xcentia_Vendors_Model_Service) {
            $service = $service->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.service_id = ?', $service);
        return $this;
    }
    
	public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
