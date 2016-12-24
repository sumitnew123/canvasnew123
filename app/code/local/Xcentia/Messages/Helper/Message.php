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
 * Message helper
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Helper_Message extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the messages list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
	 const XML_PATH_ADMIN_SENDER_EMAIL     = 'trans_email/ident_general/email';
	const XML_PATH_ADMIN_EMAIL_SENDER     ='trans_email/ident_general/name';
    public function getMessagesUrl()
    {
        if ($listKey = Mage::getStoreConfig('xcentia_messages/message/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('xcentia_messages/message/index');
    }
	public function sendMail($templatePath, $recepientEmail, $recepientName, $vars)
	{
		 // Send Transactional Email
	    $mailTemplate = Mage::getModel('core/email_template');
	    $mailTemplate->loadDefault($templatePath);
        $mailTemplate->setSenderName(Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_SENDER));
        $mailTemplate->setSenderEmail(Mage::getStoreConfig(self::XML_PATH_ADMIN_SENDER_EMAIL));
		$mailTemplate->send($recepientEmail,$recepientName,$vars);		  
		return;
		
	}
    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('xcentia_messages/message/breadcrumbs');
    }
}
