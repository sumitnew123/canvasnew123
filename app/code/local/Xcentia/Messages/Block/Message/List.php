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
class Xcentia_Messages_Block_Message_List extends Mage_Core_Block_Template
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
			$messages->addFieldToFilter('vendor_id', $customer->getId());
		}else{
			$messages->addFieldToFilter('owner',2);
			$messages->addFieldToFilter('cust_id', $customer->getId());
		}
		$messages->getSelect()->group(array('parent_id'));
       // $messages->setOrder('subject', 'asc');
        $this->setMessages($messages);
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

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
