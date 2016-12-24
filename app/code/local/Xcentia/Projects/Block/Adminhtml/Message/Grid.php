<?php
/**
 * Xcentia_Projects extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Projects
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Message admin grid block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Message_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
     * @return Xcentia_Projects_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xcentia_projects/message')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'project_id',
            array(
                'header'    => Mage::helper('xcentia_projects')->__('Project'),
                'index'     => 'project_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('xcentia_projects/project_collection')
                    ->toOptionHash(),
                'renderer'  => 'xcentia_projects/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getProjectId'
                ),
                'base_link' => 'adminhtml/projects_project/edit'
            )
        );
        $this->addColumn(
            'sender_id',
            array(
                'header'    => Mage::helper('xcentia_projects')->__('Sender'),
                'align'     => 'left',
                'index'     => 'sender_id',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('xcentia_projects')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('xcentia_projects')->__('Enabled'),
                    '0' => Mage::helper('xcentia_projects')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'message',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Message'),
                'index'  => 'message',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'has_attachment',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Has Attachment'),
                'index'  => 'has_attachment',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('xcentia_projects')->__('Yes'),
                    '0' => Mage::helper('xcentia_projects')->__('No'),
                )

            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('xcentia_projects')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('xcentia_projects')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xcentia_projects')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('xcentia_projects')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('xcentia_projects')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('xcentia_projects')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('message');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('xcentia_projects')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('xcentia_projects')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('xcentia_projects')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_projects')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('xcentia_projects')->__('Enabled'),
                            '0' => Mage::helper('xcentia_projects')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'has_attachment',
            array(
                'label'      => Mage::helper('xcentia_projects')->__('Change Has Attachment'),
                'url'        => $this->getUrl('*/*/massHasAttachment', array('_current'=>true)),
                'additional' => array(
                    'flag_has_attachment' => array(
                        'name'   => 'flag_has_attachment',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_projects')->__('Has Attachment'),
                        'values' => array(
                                '1' => Mage::helper('xcentia_projects')->__('Yes'),
                                '0' => Mage::helper('xcentia_projects')->__('No'),
                            )

                    )
                )
            )
        );
        $values = Mage::getResourceModel('xcentia_projects/project_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'project_id',
            array(
                'label'      => Mage::helper('xcentia_projects')->__('Change Project'),
                'url'        => $this->getUrl('*/*/massProjectId', array('_current'=>true)),
                'additional' => array(
                    'flag_project_id' => array(
                        'name'   => 'flag_project_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_projects')->__('Project'),
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
     * @param Xcentia_Projects_Model_Message
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
     * @return Xcentia_Projects_Block_Adminhtml_Message_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
