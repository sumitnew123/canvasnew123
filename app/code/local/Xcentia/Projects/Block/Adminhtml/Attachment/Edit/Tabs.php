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
 * Attachment admin edit tabs
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Attachment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('attachment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xcentia_projects')->__('Attachment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Attachment_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_attachment',
            array(
                'label'   => Mage::helper('xcentia_projects')->__('Attachment'),
                'title'   => Mage::helper('xcentia_projects')->__('Attachment'),
                'content' => $this->getLayout()->createBlock(
                    'xcentia_projects/adminhtml_attachment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve attachment entity
     *
     * @access public
     * @return Xcentia_Projects_Model_Attachment
     * @author Ultimate Module Creator
     */
    public function getAttachment()
    {
        return Mage::registry('current_attachment');
    }
}
