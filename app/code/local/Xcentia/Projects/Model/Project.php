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
 * Project model
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Model_Project extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xcentia_projects_project';
    const CACHE_TAG = 'xcentia_projects_project';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xcentia_projects_project';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'project';

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
        $this->_init('xcentia_projects/project');
    }

    /**
     * before save project
     *
     * @access protected
     * @return Xcentia_Projects_Model_Project
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
     * get the url to the project details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getProjectUrl()
    {
        return Mage::getUrl('xcentia_projects/project/view', array('id'=>$this->getId()));
    }

    /**
     * save project relation
     *
     * @access public
     * @return Xcentia_Projects_Model_Project
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
     * @return Xcentia_Projects_Model_Bid_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedBidsCollection()
    {
        if (!$this->hasData('_bid_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('xcentia_projects/bid_collection')
                        ->addFieldToFilter('project_id', $this->getId());
                $this->setData('_bid_collection', $collection);
            }
        }
        return $this->getData('_bid_collection');
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
