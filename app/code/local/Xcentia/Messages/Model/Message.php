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
 * Message model
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Model_Message extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_messages_message';
    const CACHE_TAG = 'xcentia_messages_message';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_messages_message';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'message';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('xcentia_messages/message');
    }

    /**
     * before save message
     *
     * @access protected
     * @return Xcentia_Messages_Model_Message
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the message details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getMessageUrl()
    {
        return Mage::getUrl('xcentia_messages/message/view', array('id'=>$this->getId()));
    }

    /**
     * get the message Message Body
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getBody()
    {
        $body = $this->getData('body');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($body);
        return $html;
    }

    /**
     * save message relation
     *
     * @access public
     * @return Xcentia_Messages_Model_Message
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve  collection
     *
     * @access public
     * @return Xcentia_Messages_Model_Attachment_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedAttachmentsCollection()
    {
        if (!$this->hasData('_attachment_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('xcentia_messages/attachment_collection')
                        ->addFieldToFilter('message_id', $this->getId());
                $this->setData('_attachment_collection', $collection);
            }
        }
        return $this->getData('_attachment_collection');
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
