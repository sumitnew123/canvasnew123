<?php
/**
 * Xcentia_Vendors extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Vendors
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Vendor comments admin block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Vendor_Comment extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller = 'adminhtml_vendor_comment';
        $this->_blockGroup = 'xcentia_vendors';
        parent::__construct();
        $this->_headerText = Mage::helper('xcentia_vendors')->__('Vendor Comments');
        $this->_removeButton('add');
    }
}
