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
 * Project front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_VendorController extends Mage_Core_Controller_Front_Action
{
	private function _getDealer($customerId) {
		$customer = Mage::getModel('customer/customer')->load($customerId);
		$dealer = Mage::getModel('xcentia_vendors/vendor')->load($customer->getEmail(), 'email');
		return $dealer;
	}
	
	public function profileAction() {
		$id = $this->getRequest()->getParam('id', null);
		if($id > 0) {
			$dealer = $this->_getDealer($id);
			$response = array('fname' => $dealer->getFirstname(),
							  'lname' => $dealer->getLastname(),
							  'email' => $dealer->getEmail(),
							  'business_name' => $dealer->getTitle(),
							  'logo' => (string)Mage::helper('xcentia_vendors/vendor_image')->init($dealer, 'logo'),
							  'cover' => (string)Mage::helper('xcentia_vendors/vendor_image')->init($dealer, 'cover'),
							  'tagline' => $dealer->getTagline(),
							  'phone' => $dealer->getPhone(),
							  'description' => $dealer->getAbout(),
							  'area' => $dealer->getAddress1() . ', ' . $dealer->getAddress2(), 
							  'street_address1' => $dealer->getAddress1(),
							  'street_address2' => $dealer->getAddress2(),
							  'zip' => $dealer->getZip(),
							  'country' => $dealer->getCountry(),
							  'state' => $dealer->getState(),
							  'city' => $dealer->getCity(),
							);
			
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode( $response ));
	}


	public function editprofileAction() {	

		try {
			$response = array('status' => 'success', 'message' => 'The Vendor information has been saved.' );
		} catch (Mage_Core_Exception $e) {
			$response = array('status' => 'error', 'message' => $e->getMessage() );
		} catch (Exception $e) {
			$response = array('status' => 'error', 'message' => 'Cannot save the Vendor Info.' );
		}

		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function projectsAction() {		
		$id = $this->getRequest()->getParam('id', null);
		$dealer = $this->_getDealer($id);

		$projects = Mage::getResourceModel('xcentia_projects/project_collection')
                         ->addFieldToFilter('main_table.status', 1 );
        $projects->join(array('bids'=> 'xcentia_projects/bid'),'bids.project_id=main_table.entity_id', array('amount'=>'amount'), null,'left');
        $projects->addFieldToFilter('vendor_id', $dealer->getId() );
        
        
        $pastprojects = Mage::getResourceModel('xcentia_projects/project_collection')
                         ->addFieldToFilter('main_table.status', array('neq' => 1) );
        $pastprojects->join(array('bids'=> 'xcentia_projects/bid'),'bids.project_id=main_table.entity_id', array('amount'=>'amount'), null,'left');
        $pastprojects->addFieldToFilter('vendor_id', $dealer->getId() );
                         
        foreach($projects as $project) {
        	$options = json_decode($project->getOptions());
        	$desc = array();
        	foreach($options as $opname => $opval) {
        		$desc[] = strip_tags($opname.': '.$opval);
        	}
        	$response['active'][] = array('id' => $project->getId(),
        									'name' => $project->getName(),
        									'description' => $desc,
        									'totalbids' => $project->getTotalBids(),
        									'lowestbids' => $project->getLowestBid(),
        									'bidends' => $project->getBidEnd(),
        									);
        }

		foreach($pastprojects as $project) {
			$options = json_decode($project->getOptions());
			$desc = array();
        	foreach($options as $opname => $opval) {
        		$desc[] = strip_tags($opname.': '.$opval);
        	}
        	$response['past'][] = array('id' => $project->getId(),
        									'name' => $project->getName(),
        									'description' => $desc,
        									'totalbids' => $project->getTotalBids(),
        									'lowestbids' => $project->getLowestBid(),
        									'bidends' => $project->getBidEnd(),
        									);
        }

		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}

	public function ordersAction() {	                         
        $id = $this->getRequest()->getParam('id', null);
		$dealer = $this->_getDealer($id);
		
		$vendors = Mage::getResourceModel('xcentia_vendors/order_collection')
                         ->addFieldToFilter('status', 1)
                         ->addFieldToFilter('vendor_id', $dealer->getId());
        $vendor_array = array();
        foreach($vendors as $vendor) {
        	$vendor_array[] = $vendor->getOrderId();
        }
        
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', array('neq' =>'closed'))
            ->addFieldToFilter('entity_id', array('in' => $vendor_array))
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $completedorders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', 'closed' )
            ->addFieldToFilter('entity_id', array('in' => $vendor_array))
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;
        
		foreach($orders as $order) {
           	$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $order->getId());
        	$itemArray = array();
			foreach($items as $item) {
            	$itemArray[] = $item->getName();
            }
        	
        	$response['active'][] = array('id' => $order->getRealOrderId(),
        									'orderid' => $order->getId(),
        									'status' => ucfirst($order->getStatus()),
        									'items' => $itemArray,
        									'customer' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
        									'price' => Mage::helper('core')->currency($order->getGrandTotal(), true, false),
        									'payment_status' => 'Unpaid (COD)',
        									'deliverydate' => Mage::helper('core')->formatDate($item->getDeliveryDate(),'long'),
        									'posteddate' => Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(),'long'),
        									);
        }
        
		foreach($completedorders as $order) {
			$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $order->getId());
        	$itemArray = array();
			foreach($items as $item) {
            	$itemArray[] = $item->getName();
            }
        	
        	$response['past'][] = array('id' => $order->getRealOrderId(),
        									'orderid' => $order->getId(),
        									'status' => ucfirst($order->getStatus()),
        									'items' => $itemArray,
        									'customer' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
        									'price' => Mage::helper('core')->currency($order->getGrandTotal(), true, false),
        									'payment_status' => 'Unpaid (COD)',
        									'deliverydate' => Mage::helper('core')->formatDate($item->getDeliveryDate(),'long'),
        									'posteddate' => Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(),'long'),
        									);
        }
        
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}

	public function orderdetailAction() {	                         
        $id = $this->getRequest()->getParam('id', null);
        $order = Mage::getModel('sales/order')->load($id);
        
		$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $order->getId());
        $itemArray = array();
		foreach($items as $item) {
			$itemArray[] = $item->getName();
        }
        
        $response['active'] = array('id' => $order->getRealOrderId(),
        							'status' => ucfirst($order->getStatus()),
        							'items' => $itemArray,
        							'customer' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
        							'price' => Mage::helper('core')->currency($order->getGrandTotal(), true, false),
        							'payment_status' => 'Unpaid (COD)',
        							'deliverydate' => Mage::helper('core')->formatDate($item->getDeliveryDate(),'long'),
        							'posteddate' => Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(),'long'),
        						);
        $response['active'] = 'success';
        
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
}