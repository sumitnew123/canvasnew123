<?php
/**
 * Xcentia_Mobile extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Mobile
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Device admin edit tabs
 *
 * @category    Xcentia
 * @package     Xcentia_Mobile
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_Block_Adminhtml_Device_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('device_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xcentia_mobile')->__('Device'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xcentia_Mobile_Block_Adminhtml_Device_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_device',
            array(
                'label'   => Mage::helper('xcentia_mobile')->__('Device'),
                'title'   => Mage::helper('xcentia_mobile')->__('Device'),
                'content' => $this->getLayout()->createBlock(
                    'xcentia_mobile/adminhtml_device_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve device entity
     *
     * @access public
     * @return Xcentia_Mobile_Model_Device
     * @author Ultimate Module Creator
     */
    public function getDevice()
    {
        return Mage::registry('current_device');
    }
}
