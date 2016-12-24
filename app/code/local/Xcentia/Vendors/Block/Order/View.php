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
 * Order view block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Order_View extends Mage_Core_Block_Template
{
	protected $_links = array();
	
	public function _construct() {
		 parent::_construct();
		 $order = $this->getOrder();
		 if($order->getStatus() == 'pending') {
		 	$this->addLink('accept', '*/*/accept', 'Mark Getting Made');
		 	$this->addLink('cancel', '*/*/cancel', 'Cancel Order');
		 	//$this->addLink('cancel', '*/*/fraud', 'Mark Incomplete');
		 }
		 if($order->getStatus() == 'processing') {
		 	$this->addLink('complete', '*/*/packaged', 'Mark Packaged');
		 	$this->addLink('cancel', '*/*/cancel', 'Cancel Order');
		 	$this->addLink('printinvoice', '*/*/printinvoice', 'Print Invoice');
		 	//$this->addLink('cancel', '*/*/fraud', 'Mark Incomplete');
		 }
		if($order->getStatus() == 'packaged') {
		 	$this->addLink('complete', '*/*/intransit', 'Mark In-Transit');
		 	$this->addLink('cancel', '*/*/cancel', 'Cancel Order');
		 	//$this->addLink('cancel', '*/*/fraud', 'Mark Incomplete');
		 	$this->addLink('printinvoice', '*/*/printinvoice', 'Print Invoice');
		 }
		if($order->getStatus() == 'in-transit') {
		 	$this->addLink('complete', '*/*/complete', 'Mark Delivered');
		 	$this->addLink('cancel', '*/*/cancel', 'Cancel Order');
		 	//$this->addLink('cancel', '*/*/fraud', 'Mark Incomplete');
		 	$this->addLink('printinvoice', '*/*/printinvoice', 'Print Invoice');
		 	$this->addLink('printshipment', '*/*/printshipment', 'Print Packing Slip');
		 }
		 if($order->getStatus() == 'fraud') {
			$this->addLink('accept', '*/*/accept', 'Accept Order');
		 	$this->addLink('cancel', '*/*/cancel', 'Cancel Order');
		 }
	 	 if($order->getStatus() == 'complete') {
			$this->addLink('close', '*/*/close', 'Mark Delivered');
			$this->addLink('printinvoice', '*/*/printinvoice', 'Print Invoice');
		 	$this->addLink('printshipment', '*/*/printshipment', 'Print Packing Slip');
		 }
	}
	
	
	/**
	 * get the current order
	 * @access public
	 * @return mixed (Webardent_Dealer_Model_Order|null)
	 * @author Ultimate Module Creator
	 */
	public function getOrder(){
		return Mage::registry('current_order');
	}
	

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Order # %s', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }


    public function addLink($name, $path, $label)
    {
        $this->_links[$name] = new Varien_Object(array(
            'name' => $name,
            'label' => $label,
            'url' => empty($path) ? '' : Mage::getUrl($path, array('id' => $this->getOrder()->getId()))
        ));
        return $this;
    }

    public function getLinks()
    {
        $this->checkLinks();
        return $this->_links;
    }

    private function checkLinks()
    {
        $order = $this->getOrder();
        if (!$order->hasInvoices()) {
            unset($this->_links['invoice']);
        }
        if (!$order->hasShipments()) {
            unset($this->_links['shipment']);
        }
        if (!$order->hasCreditmemos()) {
            unset($this->_links['creditmemo']);
        }
    }
    
    public function getOrderHistory() {
    	$order = $this->getOrder();
    	$orderHistory = Mage::getModel('sales/order_status_history')->getCollection()
                ->addFieldToFilter('parent_id', $order->getId());
        return $orderHistory;
    }
}
