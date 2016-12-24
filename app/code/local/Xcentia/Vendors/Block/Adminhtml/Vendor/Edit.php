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
 * Vendor admin edit form
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        parent::__construct();
        $this->_blockGroup = 'xcentia_vendors';
        $this->_controller = 'adminhtml_vendor';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('xcentia_vendors')->__('Save Vendor')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('xcentia_vendors')->__('Delete Vendor')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('xcentia_vendors')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        
        $vendor = Mage::registry('current_vendor');
        if($vendor->getId() > 0) {
	        $vendor = Mage::registry('vendor_data');
	        if(!Mage::helper('xcentia_vendors/vendor')->checkVendorCreated($vendor)) {
	            $this->_addButton(
	                'approvevendor',
	                array(
	                    'label'   => Mage::helper('xcentia_vendors')->__('Approve and Create Vendor'),
	                    'onclick' => 'approvevendor()',
	                    'class'   => 'save',
	                ),
	                -100
	            );
	        }
        }

        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function approvevendor() {
                editForm.submit($('edit_form').action+'approve/yes/');
            }
        ";

        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_vendor') && Mage::registry('current_vendor')->getId()) {
            return Mage::helper('xcentia_vendors')->__(
                "Edit Vendor '%s'",
                $this->escapeHtml(Mage::registry('current_vendor')->getTitle())
            );
        } else {
            return Mage::helper('xcentia_vendors')->__('Add Vendor');
        }
    }
}
