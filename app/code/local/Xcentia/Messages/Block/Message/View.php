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
 * Message view block
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Block_Message_View extends Mage_Core_Block_Template
{
    /**
     * get the current message
     *
     * @access public
     * @return mixed (Xcentia_Messages_Model_Message|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentMessage()
    {
        return Mage::registry('current_message');
    }
	 public function getAllMessages($parentid)
    {
        $messages = Mage::getResourceModel('xcentia_messages/message_collection')
                        ->addFieldToFilter('status', 1)
						->addFieldToFilter('parent_id',$parentid);
		return $messages;						 
    }
}
