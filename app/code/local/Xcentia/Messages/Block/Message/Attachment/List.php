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
 * Message Attachments list block
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Block_Message_Attachment_List extends Xcentia_Messages_Block_Attachment_List
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $message = $this->getMessage();
        if ($message) {
            $this->getAttachments()->addFieldToFilter('message_id', $message->getId());
        }
    }

    /**
     * prepare the layout - actually do nothing
     *
     * @access protected
     * @return Xcentia_Messages_Block_Message_Attachment_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * get the current message
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
