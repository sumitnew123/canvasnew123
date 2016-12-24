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
 * Message admin edit tabs
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Block_Adminhtml_Message_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('message_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xcentia_messages')->__('Message'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xcentia_Messages_Block_Adminhtml_Message_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_message',
            array(
                'label'   => Mage::helper('xcentia_messages')->__('Message'),
                'title'   => Mage::helper('xcentia_messages')->__('Message'),
                'content' => $this->getLayout()->createBlock(
                    'xcentia_messages/adminhtml_message_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve message entity
     *
     * @access public
     * @return Xcentia_Messages_Model_Message
     * @author Ultimate Module Creator
     */
    public function getMessage()
    {
        return Mage::registry('current_message');
    }
}
