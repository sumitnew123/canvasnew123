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
 * Message list block
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author Ultimate Module Creator
 */
class Xcentia_Messages_Block_Message_Notification extends Mage_Core_Block_Template
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
		$customer = Mage::getSingleton('customer/session')->getCustomer();
        $messages = Mage::getResourceModel('xcentia_messages/message_collection')
                         ->addFieldToFilter('status', 1);
		if($customer->getGroupId()=='2'){
			$messages->addFieldToFilter('owner', 1);
			$messages->addFieldToFilter('is_read', 0);
			$messages->addFieldToFilter('type', 'invite');
			$messages->addFieldToFilter('vendor_id', $customer->getId());
		}else{
			$messages->addFieldToFilter('owner',2);
			$messages->addFieldToFilter('is_read', 0);
			$messages->addFieldToFilter('type', 'invite');
			$messages->addFieldToFilter('cust_id', $customer->getId());
		}
		$messages->getSelect()->group(array('parent_id'));
       // $messages->setOrder('subject', 'asc');
        $this->setMessages($messages);
		
		$notification = Mage::getResourceModel('xcentia_messages/message_collection')
                         ->addFieldToFilter('status', 1);
		if($customer->getGroupId()=='2'){
			$notification->addFieldToFilter('owner', 1);
			$notification->addFieldToFilter('is_read', 0);
			$notification->addFieldToFilter('type', 'messages');
			$notification->addFieldToFilter('vendor_id', $customer->getId());
			$notification->setPageSize(10);
		}else{
			$notification->addFieldToFilter('owner',2);
			$notification->addFieldToFilter('is_read', 0);
			$notification->addFieldToFilter('type', 'messages');
			$notification->addFieldToFilter('cust_id', $customer->getId());
			$notification->setPageSize(10);
		}
		$notification->getSelect()->group(array('parent_id'));
       // $messages->setOrder('subject', 'asc');
        $this->setNotifications($notification);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Messages_Block_Message_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_messages.message.html.pager'
        )
        ->setCollection($this->getMessages());
        $this->setChild('pager', $pager);
        $this->getMessages()->load();
        return $this;
    }

 
}
