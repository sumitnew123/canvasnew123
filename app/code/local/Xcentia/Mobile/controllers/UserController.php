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
class Xcentia_Mobile_UserController extends Mage_Core_Controller_Front_Action
{
	public function loginAction() {
		$username = $this->getRequest()->getParam('user', null);
		$password = $this->getRequest()->getParam('pass', null);
		$session = Mage::getSingleton('customer/session');
		try {
			if($session->login($username, $password)) {
				$response = array('status' => 'success', 'customer' => $session->getCustomer()->getId(),
				 'photo' => Netgo_Customerpic_Block_Customerpic_Photo::getCustomerPhoto(),
				 'isvendor' => (Mage::helper('customer')->getCustomer()->getGroupId() == 2) ? 1 : 0  );
			} else {
				$response = array('status' => 'mismatch', 'message' => 'Username or Password does not match');
			}
		} catch (Exception $e) {
			$response = array('status' => 'error', 'message' => $e->getMessage() );
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function signupAction() {
		$data = json_decode(file_get_contents("php://input"));
		$session = Mage::getSingleton('customer/session');
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		 
		$customer = Mage::getModel("customer/customer");
		$customer   ->setWebsiteId($websiteId)
		            ->setStore($store)
		            ->setFirstname($data->firstname)
		            ->setLastname($data->lastname)
		            ->setEmail($data->email)
		            ->setPhone($data->phone)
		            ->setPassword($data->password);
		 
		try{
		    $customer->save();
			$response = array('status' => 'success', 'customer' => $customer->getId(),
			 'photo' => Netgo_Customerpic_Block_Customerpic_Photo::getCustomerPhoto(),
			 'isvendor' => ($customer->getGroupId() == 2) ? 1 : 0  );
		}
		catch (Exception $e) {
		    $response = array('status' => 'error', 'message' => $e->getMessage() );
		}
		$this->getResponse()->clearHeaders()
							->setHeader('Access-Control-Allow-Origin','*',true)
							->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)
							->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function loadAction() {
		$custome_id = $this->getRequest()->getParam('customer', null);
		$session = Mage::getSingleton('customer/session');
		try {
			if($session->loginById($custome_id, $password)) {
				$customer = $session->getCustomer();
				$response = array('status' => 'success', 'customer' => $session->getCustomer()->getId(),
				 'photo' => Netgo_Customerpic_Block_Customerpic_Photo::getCustomerPhoto(),
				 'isvendor' => (Mage::helper('customer')->getCustomer()->getGroupId() == 2) ? 1 : 0,
				 'firstname' => $customer->getFirstname(),
				 'lastname' => $customer->getLastname(),
				 'email' => $customer->getEmail(),
				 'phone' => $customer->getPhone(),
				 'name' => $customer->getName(),
				);
			} else {
				$response = array('status' => 'mismatch', 'message' => 'Username or Password does not match');
			}
		} catch (Exception $e) {
			$response = array('status' => 'error', 'message' => $e->getMessage() );
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function addressesAction() {
		$custome_id = $this->getRequest()->getParam('customer', null);
		$customer = Mage::getModel('customer/customer')->load($custome_id);
		try {
			if($customer->getId() > 0) {
				$response['primarybilling'] = array('id' => $customer->getDefaultBilling(), 'html' => $customer->getDefaultBilling() ? $customer->getPrimaryBillingAddress()->format('html'):'You have not set a default billing address' );
				$response['primaryshipping'] = array('id' => $customer->getDefaultShipping(), 'html' => $customer->getDefaultShipping() ? $customer->getPrimaryShippingAddress()->format('html'):'You have not set a default shipping address' );
				$addtionalAddresses = $customer->getAdditionalAddresses();
				foreach($addtionalAddresses as $addtionalAddress) {
					$response['additinal'][] = array('id' => $addtionalAddress->getId(), 'html' => $addtionalAddress->format('html') );
				}
				
			} else {
				$response = array('status' => 'error', 'message' => 'Cannot find the User.');
			}
		} catch (Exception $e) {
			$response = array('status' => 'error', 'message' => $e->getMessage() );
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function getaddressAction() {
		$address_id = $this->getRequest()->getParam('address', null);
		$address   = Mage::getModel('customer/address')->load($address_id);
		$street = $address->getStreet();
		$addressData = array('firstname' => $address->getFirstname(),
							'lastname' => $address->getLastname(),
							'city' => $address->getCity(),
							'telephone' => $address->getTelephone(),
							'zip' => $address->getPostcode(),
							'country_id' => $address->getCountryId(),
							'region' => $address->getRegion(),
							'street1' => $street[0],
							'street2' => $street[1],
							);
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($addressData));
	}
	
	
	public function addaddressAction() {
		$custome_id = $this->getRequest()->getParam('customer', null);
		$data = json_decode(file_get_contents("php://input"));
		$customer = Mage::getModel('customer/customer')->load($custome_id);
		
		if($custome_id > 0) {
			$address   = Mage::getModel('customer/address');
			//$data['street'][] = $data['street1'];
            //$data['street'][] = $data['street2'];
            //$data['country_id'] = 'IN';
            $_custom_address = array (
			    'firstname' => $data->firstname,
			    'lastname' => $data->lastname,
			    'street' => array (
			        '0' => $data->street1,
			        '1' => $data->street2,
			    ),
			    'city' => $data->city,
			    'region_id' => '',
			    'region' => '',
			    'postcode' => $data->zip,
			    'country_id' => 'IN', /* Croatia */
			    'telephone' => $data->telephone,
			);
            $address->setData($_custom_address)->setCustomerId($custome_id)->setSaveInAddressBook('1');	

            try {
			    $address->save();
			    $response = array('status' => 'success', 'message' => 'Address saved successfully');
			}
			catch (Exception $ex) {
			    $response = array('status' => 'error', 'message' => 'Some error while saving Address');
			}
 			
		} else {
			$response = array('status' => 'error', 'message' => 'Cannot find the User.');
		}

		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function passworddataAction() {
		$data = json_decode(file_get_contents("php://input"));
		$customer = Mage::getModel("customer/customer")->load($data->customer);
		
		$currPass   = $data->current_password;
        $newPass    = $data->new_password;
        $confPass   = $data->confirm_password;
        
		$oldPass = $customer->getPasswordHash();
        if ( Mage::helper('core/string')->strpos($oldPass, ':')) {
        	list($_salt, $salt) = explode(':', $oldPass);
		} else {
			$salt = false;
		}

		if ($customer->hashPassword($currPass, $salt) == $oldPass) {
			if (strlen($newPass)) {
				$customer->setPassword($newPass);
				$customer->setPasswordConfirmation($confPass);
			} else {
				$errors[] = $this->__('New password field cannot be empty.');
			}
		} else {
			$errors[] = $this->__('Invalid current password');
		}
		
		if (!empty($errors)) {
			$response = array('status' => 'error', 'message' => implode(', ', $errors) );
			$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        	$this->getResponse()->setBody(json_encode($response));
        	exit;
        }
		
		try {
			$customer->cleanPasswordsValidationData();
			$customer->save();
			$response = array('status' => 'success', 'message' => 'Your Password is changed successfully!.' );
			$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        	$this->getResponse()->setBody(json_encode($response));
        } catch (Mage_Core_Exception $e) {
        	$response = array('status' => 'error', 'message' => $e->getMessage() );
			$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        	$this->getResponse()->setBody(json_encode($response));
		} catch (Exception $e) {
			$response = array('status' => 'error', 'message' => 'Cannot save the Password.' );
			$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        	$this->getResponse()->setBody(json_encode($response));
		}
	}
	
	public function editprofileAction() {
		$data = json_decode(file_get_contents("php://input"));
		$customer = Mage::getModel("customer/customer")->load($data->customer);
	
		try {
			$customer->setFirstname($data->firstname);
			$customer->setLastname($data->lastname);
			$customer->setEmail($data->email);
			$customer->setPhone($data->phone);
			$customer->save();
			$response = array('status' => 'success', 'message' => 'The account information has been saved.' );
		} catch (Mage_Core_Exception $e) {
			$response = array('status' => 'error', 'message' => $e->getMessage() );
		} catch (Exception $e) {
			$response = array('status' => 'error', 'message' => 'Cannot save the customer.' );
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Access-Control-Allow-Methods','GET, POST, PATCH, PUT, DELETE, OPTIONS',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function projectsAction() {
		$custome_id = $this->getRequest()->getParam('customer', null);
		
		$projects = Mage::getResourceModel('xcentia_projects/project_collection')
                         ->addFieldToFilter('owner_id', $custome_id )
                         ->addFieldToFilter('status', 1 );
                         
        $pastprojects = Mage::getResourceModel('xcentia_projects/project_collection')
                         ->addFieldToFilter('owner_id', $custome_id )
                         ->addFieldToFilter('status', array('neq' => 1) );
                         
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
}