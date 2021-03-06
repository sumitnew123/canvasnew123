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
 * Attachment model
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Model_Attachment extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_projects_attachment';
    const CACHE_TAG = 'xcentia_projects_attachment';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_projects_attachment';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'attachment';

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
        $this->_init('xcentia_projects/attachment');
    }

    /**
     * before save attachment
     *
     * @access protected
     * @return Xcentia_Projects_Model_Attachment
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
     * save attachment relation
     *
     * @access public
     * @return Xcentia_Projects_Model_Attachment
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Xcentia_Projects_Model_Message
     * @author Ultimate Module Creator
     */
    public function getParentMessage()
    {
        if (!$this->hasData('_parent_message')) {
            if (!$this->getMessageId()) {
                return null;
            } else {
                $message = Mage::getModel('xcentia_projects/message')
                    ->load($this->getMessageId());
                if ($message->getId()) {
                    $this->setData('_parent_message', $message);
                } else {
                    $this->setData('_parent_message', null);
                }
            }
        }
        return $this->getData('_parent_message');
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
