<?php
/**
 * Xcentia_Messages extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Messages
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Message admin grid block
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Block_Adminhtml_Message_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('messageGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Xcentia_Messages_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xcentia_messages/message')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Xcentia_Messages_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'subject',
            array(
                'header'    => Mage::helper('xcentia_messages')->__('Subject'),
                'align'     => 'left',
                'index'     => 'subject',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('xcentia_messages')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('xcentia_messages')->__('Enabled'),
                    '0' => Mage::helper('xcentia_messages')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'cust_id',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Customer Id'),
                'index'  => 'cust_id',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'vendor_id',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Vendor Id'),
                'index'  => 'vendor_id',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'owner',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Owner'),
                'index'  => 'owner',
                'type'  => 'options',
                'options' => Mage::helper('xcentia_messages')->convertOptions(
                    Mage::getModel('xcentia_messages/message_attribute_source_owner')->getAllOptions(false)
                )

            )
        );
        $this->addColumn(
            'is_read',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Read'),
                'index'  => 'is_read',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'parent_id',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Parent Id'),
                'index'  => 'parent_id',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'has_attachment',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Attachments'),
                'index'  => 'has_attachment',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('xcentia_messages')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('xcentia_messages')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xcentia_messages')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('xcentia_messages')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('xcentia_messages')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('xcentia_messages')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Xcentia_Messages_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('message');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('xcentia_messages')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('xcentia_messages')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('xcentia_messages')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_messages')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('xcentia_messages')->__('Enabled'),
                            '0' => Mage::helper('xcentia_messages')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'owner',
            array(
                'label'      => Mage::helper('xcentia_messages')->__('Change Owner'),
                'url'        => $this->getUrl('*/*/massOwner', array('_current'=>true)),
                'additional' => array(
                    'flag_owner' => array(
                        'name'   => 'flag_owner',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_messages')->__('Owner'),
                        'values' => Mage::getModel('xcentia_messages/message_attribute_source_owner')
                            ->getAllOptions(true),

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
     * @param Xcentia_Messages_Model_Message
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
     * @return Xcentia_Messages_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
