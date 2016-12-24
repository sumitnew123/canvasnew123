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
 * Message admin block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Message extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_message';
        $this->_blockGroup         = 'xcentia_projects';
        parent::__construct();
        $this->_headerText         = Mage::helper('xcentia_projects')->__('Message');
        $this->_updateButton('add', 'label', Mage::helper('xcentia_projects')->__('Add Message'));

    }
}
