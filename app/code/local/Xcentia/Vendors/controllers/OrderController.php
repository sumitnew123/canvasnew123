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
 * Order front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_OrderController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch()
	{
		// a brute-force protection here would be nice
		parent::preDispatch();

		if (!$this->getRequest()->isDispatched()) {
			return;
		}
		if (!$this->_getSession()->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		} else {
			$this->_getSession()->setNoReferer(true);
		}
	}
	/**
	 * view plan action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('catalog/session');

		$this->getLayout()->getBlock('head')->setTitle($this->__('My Orders'));

		if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
			$block->setRefererUrl($this->_getRefererUrl());
		}
		$this->renderLayout();
	}

	public function viewAction(){
		if (null === $orderId) {
			$orderId = (int) $this->getRequest()->getParam('order_id');
		}
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}

		$order = Mage::getModel('sales/order')->load($orderId);
		//Mage::register('current_order', $order);

		if ($this->_canViewOrder($order)) {
			Mage::register('current_order', $order);
		} else {
			$this->_redirect('*/*/index');
			return;
		}

		$this->loadLayout();
		$this->_initLayoutMessages('catalog/session');
		$this->renderLayout();

	}

	protected function _canViewOrder($order)
	{
		$dealer_id = Mage::helper('xcentia_vendors')->getDealer()->getId();
		$dealerOrder = Mage::getModel('xcentia_vendors/order')->load($order->getId(), 'order_id');
		if ($dealerOrder->getVendorId() == $dealer_id) {
			return true;
		}
		Mage::getSingleton('catalog/session')->addError('Sorry, you are not authorised to view this order');
		return false;
	}


	public function acceptAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderId);
		
		try {
			if(!$order->canInvoice()) {
				Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
			}
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			if (!$invoice->getTotalQty()) {
				Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
			}

			$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
			$invoice->register();
			$transactionSave = Mage::getModel('core/resource_transaction')
								->addObject($invoice)
								->addObject($invoice->getOrder());
			$transactionSave->save();
		}
		catch (Mage_Core_Exception $e) {
			Mage::getSingleton('catalog/session')->addError('Error creating Invoice. Please try later');
			$this->_redirect('*/*/view', array('order_id' => $orderId));
			return;
		}
		
		$this->_changeStatus($order, 'processing', 'Order status has been changed to Getting Made by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to Getting Made');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}
	
	public function packagedAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderId);
			try {
				if(!$order->canShip()) {
					Mage::throwException(Mage::helper('core')->__('Cannot create an shipment.'));
				}
				$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment();
				if (!$shipment->getTotalQty()) {
					Mage::throwException(Mage::helper('core')->__('Cannot create an shipment without products.'));
				}
				
				$shipment->register();
				$transactionSave = Mage::getModel('core/resource_transaction')
									->addObject($shipment)
									->addObject($shipment->getOrder());
				$transactionSave->save();
			}
			catch (Mage_Core_Exception $e) {
				Mage::getSingleton('catalog/session')->addError('Error creating Shipment. Please try later. ' . $e->getMessage());
				$this->_redirect('*/*/view', array('order_id' => $orderId));
				return;
			}
		$this->_changeStatus($order, 'packaged', 'Order status has been changed to Packaged by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to Packaged');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}
	
	public function printinvoiceAction()
    {
        if ($Id = $this->getRequest()->getParam('id')) {
        	$order = Mage::getModel('sales/order')->load($Id);
            if ($invoice = Mage::getModel('sales/order_invoice')->load($Id, 'order_id')) {
                $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
                $this->_prepareDownloadResponse('Invoice-'.$order->getIncrementId().
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }
    
	public function printshipmentAction()
    {
        if ($Id = $this->getRequest()->getParam('id')) {
        	$order = Mage::getModel('sales/order')->load($Id);
            if ($shipment = Mage::getModel('sales/order_shipment')->load($Id, 'order_id')) {
                $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($shipment));
                $this->_prepareDownloadResponse('Packing Slip -'.$order->getIncrementId().
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }
    
    
	public function intransitAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		
		$this->_changeStatus($order, 'in-transit', 'Order status has been changed to In-Transit by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to In-Transit');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}

	public function completeAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderId);
		$this->_changeStatus($order, 'complete', 'Order status has been changed to Delivered by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to Delivered');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}
	public function cancelAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderId);
		$this->_changeStatus($order, 'cancel', 'Order status has been changed to Cancelled by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to Cancel');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}
	public function fraudAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderId);
		$this->_changeStatus($order, 'fraud', 'Order status has been changed to Incomplete by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to Incomplete');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}
	public function closeAction(){
		$orderId = (int) $this->getRequest()->getParam('id');
		if (!$orderId) {
			$this->_forward('noRoute');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderId);
		$this->_changeStatus($order, 'closed', 'Order status has been changed to Delivered by the Vendor');
		Mage::getSingleton('catalog/session')->addSuccess('Changed the order status to Delivered');
		$this->_redirect('*/*/view', array('order_id' => $orderId));
	}

	protected function _changeStatus($order, $status, $comment) {
		$order->addStatusHistoryComment($comment, $status)
		->setIsVisibleOnFront(true)
		->setIsCustomerNotified(true);
		$order->save();
		$order->sendOrderUpdateEmail(true, $comment);
	}
}
