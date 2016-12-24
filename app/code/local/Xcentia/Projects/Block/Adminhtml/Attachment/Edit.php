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
 * Attachment admin edit form
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Attachment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'xcentia_projects';
        $this->_controller = 'adminhtml_attachment';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('xcentia_projects')->__('Save Attachment')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('xcentia_projects')->__('Delete Attachment')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('xcentia_projects')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
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
        if (Mage::registry('current_attachment') && Mage::registry('current_attachment')->getId()) {
            return Mage::helper('xcentia_projects')->__(
                "Edit Attachment '%s'",
                $this->escapeHtml(Mage::registry('current_attachment')->getName())
            );
        } else {
            return Mage::helper('xcentia_projects')->__('Add Attachment');
        }
    }
}
