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
 * Contact admin edit tabs
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Contact_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('contact_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xcentia_vendors')->__('Contact'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Contact_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_contact',
            array(
                'label'   => Mage::helper('xcentia_vendors')->__('Contact'),
                'title'   => Mage::helper('xcentia_vendors')->__('Contact'),
                'content' => $this->getLayout()->createBlock(
                    'xcentia_vendors/adminhtml_contact_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve contact entity
     *
     * @access public
     * @return Xcentia_Vendors_Model_Contact
     * @author Ultimate Module Creator
     */
    public function getContact()
    {
        return Mage::registry('current_contact');
    }
}
