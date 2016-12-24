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
 * Vendor comments resource collection model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection extends Xcentia_Vendors_Model_Resource_Vendor_Collection
{
    /**
     * Entities alias
     *
     * @var array
     */
    protected $_entitiesAlias        = array();

    /**
     * construct
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_init('xcentia_vendors/vendor');
        $this->_setIdFieldName('comment_id');
    }

    /**
     * init select
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Add customer filter
     *
     * @access public
     * @param int $customerId
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('ct.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add entity filter
     *
     * @access public
     * @param int $entityId
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    public function addEntityFilter($entityId)
    {
        $this->getSelect()->where('ct.vendor_id = ?', $entityId);
        return $this;
    }

    /**
     * Add status filter
     *
     * @access public
     * @param mixed $status
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    public function addStatusFilter($status = 1)
    {
        $this->getSelect()->where('ct.status = ?', $status);
        return $this;
    }

    /**
     * Set date order
     *
     * @access public
     * @param string $dir
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('ct.created_at', $dir);
        return $this;
    }

    /**
     * join fields to entity
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    protected function _joinFields()
    {
        $commentTable = Mage::getSingleton('core/resource')
            ->getTableName('xcentia_vendors/vendor_comment');
        $this->getSelect()->join(
            array('ct' => $commentTable),
            'ct.vendor_id = main_table.entity_id',
            array(
                'ct_title'      => 'title',
                'ct_comment_id' => 'comment_id',
                'ct_name'       => 'name',
                'ct_status'     => 'status',
                'ct_email'      => 'email',
                'ct_created_at' => 'created_at',
                'ct_updated_at' => 'updated_at'
            )
        );
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @access public
     * @param mixed $limit
     * @param mixed $offset
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('ct.comment_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Retrieves column values
     *
     * @access public
     * @param string $colName
     * @return array
     * @author Ultimate Module Creator
     */
    public function getColumnValues($colName)
    {
        $col = array();
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }
    /**
     * Render SQL for retrieve product count
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('COUNT(main_table.entity_id)')
            ->reset(Zend_Db_Select::HAVING);

        return $select;
    }

    /**
     * Add attribute to filter
     *
     * @access public
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Xcentia_Vendors_Model_Resource_Vendor_Comment_Vendor_Collection
     * @author Ultimate Module Creator
     */
    public function addFieldToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch($attribute) {
            case 'ct.comment_id':
            case 'ct.created_at':
            case 'ct.status':
            case 'ct.title':
            case 'ct.name':
            case 'ct.email':
            case 'ct.comment':
            case 'ct.updated_at':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;

            default:
                parent::addFieldToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }
}
