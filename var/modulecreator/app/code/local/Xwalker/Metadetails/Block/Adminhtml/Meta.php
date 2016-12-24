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
 * Meta admin block
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Block_Adminhtml_Meta extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_meta';
        $this->_blockGroup         = 'xwalker_metadetails';
        parent::__construct();
        $this->_headerText         = Mage::helper('xwalker_metadetails')->__('Meta');
        $this->_updateButton('add', 'label', Mage::helper('xwalker_metadetails')->__('Add Meta'));

    }
}
