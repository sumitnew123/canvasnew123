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
 * Contact edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Contact_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Contact_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('contact_');
        $form->setFieldNameSuffix('contact');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'contact_form',
            array('legend' => Mage::helper('xcentia_vendors')->__('Contact'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Name'),
                'name'  => 'name',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'email',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Email'),
                'name'  => 'email',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'mobile',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Mobile No'),
                'name'  => 'mobile',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'vendor_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Vendor Id'),
                'name'  => 'vendor_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('xcentia_vendors')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('xcentia_vendors')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('xcentia_vendors')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_contact')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getContactData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getContactData());
            Mage::getSingleton('adminhtml/session')->setContactData(null);
        } elseif (Mage::registry('current_contact')) {
            $formValues = array_merge($formValues, Mage::registry('current_contact')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
