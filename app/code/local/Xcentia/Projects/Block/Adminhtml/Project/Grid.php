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
 * Project admin grid block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Project_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('projectGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Project_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xcentia_projects/project')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Project_Grid
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
            'name',
            array(
                'header'    => Mage::helper('xcentia_projects')->__('Project Name'),
                'align'     => 'left',
                'index'     => 'name',
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
            'quantity',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Quantity'),
                'index'  => 'quantity',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'budget',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Budget (per piece)'),
                'index'  => 'budget',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'expected',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Expected Time'),
                'index'  => 'expected',
                'type'=> 'date',

            )
        );
        $this->addColumn(
            'type',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Project Type'),
                'index'  => 'type',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'owner_id',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Owner'),
                'index'  => 'owner_id',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'is_private',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Is Private'),
                'index'  => 'is_private',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('xcentia_projects')->__('Yes'),
                    '0' => Mage::helper('xcentia_projects')->__('No'),
                )

            )
        );
        $this->addColumn(
            'is_single',
            array(
                'header' => Mage::helper('xcentia_projects')->__('Is Single'),
                'index'  => 'is_single',
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
     * @return Xcentia_Projects_Block_Adminhtml_Project_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('project');
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
            'is_private',
            array(
                'label'      => Mage::helper('xcentia_projects')->__('Change Is Private'),
                'url'        => $this->getUrl('*/*/massIsPrivate', array('_current'=>true)),
                'additional' => array(
                    'flag_is_private' => array(
                        'name'   => 'flag_is_private',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_projects')->__('Is Private'),
                        'values' => array(
                                '1' => Mage::helper('xcentia_projects')->__('Yes'),
                                '0' => Mage::helper('xcentia_projects')->__('No'),
                            )

                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'is_single',
            array(
                'label'      => Mage::helper('xcentia_projects')->__('Change Is Single'),
                'url'        => $this->getUrl('*/*/massIsSingle', array('_current'=>true)),
                'additional' => array(
                    'flag_is_single' => array(
                        'name'   => 'flag_is_single',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('xcentia_projects')->__('Is Single'),
                        'values' => array(
                                '1' => Mage::helper('xcentia_projects')->__('Yes'),
                                '0' => Mage::helper('xcentia_projects')->__('No'),
                            )

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
     * @param Xcentia_Projects_Model_Project
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
     * @return Xcentia_Projects_Block_Adminhtml_Project_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
