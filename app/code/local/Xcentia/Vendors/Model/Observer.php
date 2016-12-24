<?php
/**
 * Webardent_Dealer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Webardent
 * @package		Webardent_Dealer
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Service model
 *
 * @category	Webardent
 * @package		Webardent_Dealer
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Model_Observer {
	
	public function newOrderPlaced(Varien_Event_Observer $observer) {
		$order = $observer->getEvent()->getOrder();
		try{
			$products = $order->getAllItems();
			foreach($products as $product) {
				$product = Mage::getModel('catalog/product')->load($product->getProductId());
				break;
			}
			$dealerOrder = Mage::getModel('xcentia_vendors/order');
			$data = array(	'vendor_id'=>((int)$product->getDealerId() > 0) ? $product->getDealerId() : Mage::getSingleton('checkout/session')->getOrderVendorId(),
							'customer'=> $order->getCustomer()->getName(),
							'customer_id'=> $order->getCustomer()->getId(),
							'increment_id'=> $order->getIncrementId(),
							'order_id'=> $order->getId(),
							'status'=> 1,
						);
			$dealerOrder->setData($data)->save();
			Mage::getSingleton('checkout/session')->setOrderVendorId(0);
			Mage::getSingleton('checkout/session')->setQuoteProjectId(0);
		} catch (Exception $e) {
			Mage::logException($e);
		}
	}
	
	public function vendorFeaturedProducts() {
		$vendors = Mage::getModel('xcentia_vendors/vendor')
					->getCollection()
					->addFieldToFilter('status', 1);
		$vendors->getSelect()->order("updated_at asc")->limit(5);
		foreach($vendors as $vendor) {
			$prodjson = array();
			$products = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSelect(array('small_image','is_featured'))
					->addAttributeToFilter('dealer_id', $vendor->getId());
			$count = $products->getSize();
			//$products->addAttributeToFilter('is_featured', 1)
			$products->addFieldToFilter('type_id','simple')
					->addUrlRewrite()
					->addAttributeToSort('is_featured', 'DESC')
					->setPageSize(3);
				foreach($products as $product) {
					$prodjson[] = array('id'=>$product->getId(), 'url'=>$product->getProductUrl(), 'image'=>(string)Mage::helper('catalog/image')->init($product, 'small_image')->keepFrame(false)->resize(100));
				}
			$vendor->setProductCount($count);
			$vendor->setFeaturedProducts(json_encode($prodjson));
			$vendor->save();
		}
	}
	
	public function cookiecheck(Varien_Event_Observer $observer) {
		$cookie = Mage::getSingleton('core/cookie');
		$city = $cookie->get('city');
		try {
			if(!isset($city) or $city == '') {
				$iClient = new Varien_Http_Client();
	            $iClient->setUri('https://freegeoip.net/json/'.Mage::helper('core/http')->getRemoteAddr())
	        	->setMethod('GET')
	       		->setConfig(array(
	                'maxredirects'=>0,
	                'timeout'=>30,
	        	));  
	    		$response = $iClient->request();
	    		$data = json_decode($response->getBody());
	    		if($data->city != '') {
					$period = 3600;
					$path = '/';
					//$domain = 'dm.com';
					$secure = false;
					$httponly = false;
					$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
					try {
					    $connection->beginTransaction();
						$country = Mage::getModel('lookups/country');
						$country->addCountryStateCity($data);
						// Make saves and other actions that affect the database
					    $connection->commit();
					    
					    $cookie->set('city', $data->city, $period, $path, $domain, $secure, $httponly);
						$cookie->set('region', $data->region_name, $period, $path, $domain, $secure, $httponly);
						$cookie->set('country', $data->country_code, $period, $path, $domain, $secure, $httponly);
						$cookie->set('latitude', $data->latitude, $period, $path, $domain, $secure, $httponly);
						$cookie->set('longitude', $data->longitude, $period, $path, $domain, $secure, $httponly);
						
					} catch (Exception $e) {
					    $connection->rollback();
					}
				}
			}
			
		} catch (Exception $e) {
			return;
		}
	}
}