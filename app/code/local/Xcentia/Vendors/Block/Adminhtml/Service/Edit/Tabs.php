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
 * Service admin edit tabs
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Service_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('service_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xcentia_vendors')->__('Service'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Service_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_service',
            array(
                'label'   => Mage::helper('xcentia_vendors')->__('Service'),
                'title'   => Mage::helper('xcentia_vendors')->__('Service'),
                'content' => $this->getLayout()->createBlock(
                    'xcentia_vendors/adminhtml_service_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve service entity
     *
     * @access public
     * @return Xcentia_Vendors_Model_Service
     * @author Ultimate Module Creator
     */
    public function getService()
    {
        return Mage::registry('current_service');
    }
}
