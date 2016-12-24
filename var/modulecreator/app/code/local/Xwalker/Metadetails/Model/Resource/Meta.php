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
 * Meta resource model
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Model_Resource_Meta extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        $this->_init('xwalker_metadetails/meta', 'entity_id');
    }
}
