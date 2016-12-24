<?php
/**
 * Xcentia_Mobile extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Mobile
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Device admin block
 *
 * @category    Xcentia
 * @package     Xcentia_Mobile
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_Block_Adminhtml_Device extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_device';
        $this->_blockGroup         = 'xcentia_mobile';
        parent::__construct();
        $this->_headerText         = Mage::helper('xcentia_mobile')->__('Device');
        $this->_updateButton('add', 'label', Mage::helper('xcentia_mobile')->__('Add Device'));

    }
}
