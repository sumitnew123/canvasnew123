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
 * Vendor comment admin edit tabs
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Vendor_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('vendor_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xcentia_vendors')->__('Vendor Comment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Vendor_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_vendor_comment',
            array(
                'label'   => Mage::helper('xcentia_vendors')->__('Vendor comment'),
                'title'   => Mage::helper('xcentia_vendors')->__('Vendor comment'),
                'content' => $this->getLayout()->createBlock(
                    'xcentia_vendors/adminhtml_vendor_comment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve comment
     *
     * @access public
     * @return Xcentia_Vendors_Model_Vendor_Comment
     * @author Ultimate Module Creator
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
