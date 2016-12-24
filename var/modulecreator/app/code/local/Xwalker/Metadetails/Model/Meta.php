<?php
/**
 * Xwalker_Metadetails extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xwalker
 * @package        Xwalker_Metadetails
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Meta model
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Model_Meta extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'xwalker_metadetails_meta';
    const CACHE_TAG = 'xwalker_metadetails_meta';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'xwalker_metadetails_meta';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'meta';

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
        $this->_init('xwalker_metadetails/meta');
    }

    /**
     * before save meta
     *
     * @access protected
     * @return Xwalker_Metadetails_Model_Meta
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
     * save meta relation
     *
     * @access public
     * @return Xwalker_Metadetails_Model_Meta
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
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
