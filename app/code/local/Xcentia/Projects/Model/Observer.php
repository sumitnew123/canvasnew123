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
class Xcentia_Projects_Model_Observer {
	public function addprojecttoquote(Varien_Event_Observer $observer) {
		$session = Mage::getSingleton('checkout/session');
		if((int)$session->getQuoteProjectId() > 0) {
			$quoteItem = $observer->getEvent()->getQuoteItem();
			$quoteItem->setProjectId($session->getQuoteProjectId());
			$quoteItem->setDeliveryDate($session->getQuoteDeliveryDate());
			$session->setQuoteProjectId(0);
			$session->setQuoteDeliveryDate(0);
		}
	}
}