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
 * Meta admin edit form
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Block_Adminhtml_Meta_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'xwalker_metadetails';
        $this->_controller = 'adminhtml_meta';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('xwalker_metadetails')->__('Save Meta')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('xwalker_metadetails')->__('Delete Meta')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('xwalker_metadetails')->__('Save And Continue Edit'),
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
        if (Mage::registry('current_meta') && Mage::registry('current_meta')->getId()) {
            return Mage::helper('xwalker_metadetails')->__(
                "Edit Meta '%s'",
                $this->escapeHtml(Mage::registry('current_meta')->getUrl())
            );
        } else {
            return Mage::helper('xwalker_metadetails')->__('Add Meta');
        }
    }
}
