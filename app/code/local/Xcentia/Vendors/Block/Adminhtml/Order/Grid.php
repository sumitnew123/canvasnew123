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
 * Order admin grid block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('orderGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Order_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xcentia_vendors/order')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Order_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('xcentia_vendors')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'vendor_id',
            array(
                'header'    => Mage::helper('xcentia_vendors')->__('Vendor'),
                'index'     => 'vendor_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('xcentia_vendors/vendor_collection')
                    ->toOptionHash(),
                'renderer'  => 'xcentia_vendors/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getVendorId'
                ),
                'base_link' => 'adminhtml/vendors_vendor/edit'
            )
        );
        $this->addColumn(
            'order_id',
            array(
                'header'    => Mage::helper('xcentia_vendors')->__('Order Id'),
                'align'     => 'left',
                'index'     => 'order_id',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('xcentia_vendors')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('xcentia_vendors')->__('Enabled'),
                    '0' => Mage::helper('xcentia_vendors')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('xcentia_vendors')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('xcentia_vendors')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('xcentia_vendors')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xcentia_vendors')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('xcentia_vendors')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('xcentia_vendors')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('xcentia_vendors')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Order_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('xcentia_vendors')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('xcentia_vendors')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('xcentia_vendors')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_vendors')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('xcentia_vendors')->__('Enabled'),
                            '0' => Mage::helper('xcentia_vendors')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $values = Mage::getResourceModel('xcentia_vendors/vendor_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'vendor_id',
            array(
                'label'      => Mage::helper('xcentia_vendors')->__('Change Vendor'),
                'url'        => $this->getUrl('*/*/massVendorId', array('_current'=>true)),
                'additional' => array(
                    'flag_vendor_id' => array(
                        'name'   => 'flag_vendor_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_vendors')->__('Vendor'),
                        'values' => $values
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Xcentia_Vendors_Model_Order
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Order_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
