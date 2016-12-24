<?php
/**
 * Xcentia_Messages extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Messages
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Message edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Block_Adminhtml_Message_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Messages_Block_Adminhtml_Message_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('message_');
        $form->setFieldNameSuffix('message');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'message_form',
            array('legend' => Mage::helper('xcentia_messages')->__('Message'))
        );
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        $fieldset->addField(
            'subject',
            'text',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Subject'),
                'name'  => 'subject',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'cust_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Customer Id'),
                'name'  => 'cust_id',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'vendor_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Vendor Id'),
                'name'  => 'vendor_id',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'owner',
            'select',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Owner'),
                'name'  => 'owner',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('xcentia_messages/message_attribute_source_owner')->getAllOptions(true),
           )
        );

        $fieldset->addField(
            'body',
            'editor',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Message Body'),
                'name'  => 'body',
            'config' => $wysiwygConfig,
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'is_read',
            'text',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Read'),
                'name'  => 'is_read',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'parent_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Parent Id'),
                'name'  => 'parent_id',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'has_attachment',
            'text',
            array(
                'label' => Mage::helper('xcentia_messages')->__('Attachments'),
                'name'  => 'has_attachment',
            'required'  => true,
            'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('xcentia_messages')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('xcentia_messages')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('xcentia_messages')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_message')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getMessageData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getMessageData());
            Mage::getSingleton('adminhtml/session')->setMessageData(null);
        } elseif (Mage::registry('current_message')) {
            $formValues = array_merge($formValues, Mage::registry('current_message')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
