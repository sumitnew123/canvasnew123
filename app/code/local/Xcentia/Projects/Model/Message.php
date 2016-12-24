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
 * Message model
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Model_Message extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_projects_message';
    const CACHE_TAG = 'xcentia_projects_message';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_projects_message';

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
        $this->_init('xcentia_projects/message');
    }

    /**
     * before save message
     *
     * @access protected
     * @return Xcentia_Projects_Model_Message
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
     * save message relation
     *
     * @access public
     * @return Xcentia_Projects_Model_Message
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
     * @return Xcentia_Projects_Model_Attachment_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedAttachmentsCollection()
    {
        if (!$this->hasData('_attachment_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('xcentia_projects/attachment_collection')
                        ->addFieldToFilter('message_id', $this->getId());
                $this->setData('_attachment_collection', $collection);
            }
        }
        return $this->getData('_attachment_collection');
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Xcentia_Projects_Model_Project
     * @author Ultimate Module Creator
     */
    public function getParentProject()
    {
        if (!$this->hasData('_parent_project')) {
            if (!$this->getProjectId()) {
                return null;
            } else {
                $project = Mage::getModel('xcentia_projects/project')
                    ->load($this->getProjectId());
                if ($project->getId()) {
                    $this->setData('_parent_project', $project);
                } else {
                    $this->setData('_parent_project', null);
                }
            }
        }
        return $this->getData('_parent_project');
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
