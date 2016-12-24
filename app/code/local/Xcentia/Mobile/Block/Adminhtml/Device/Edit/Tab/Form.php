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
 * Device edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Mobile
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_Block_Adminhtml_Device_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Mobile_Block_Adminhtml_Device_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('device_');
        $form->setFieldNameSuffix('device');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'device_form',
            array('legend' => Mage::helper('xcentia_mobile')->__('Device'))
        );

        $fieldset->addField(
            'gcm_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('GSM ID'),
                'name'  => 'gcm_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'device_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('Device Id'),
                'name'  => 'device_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'platform',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('Platform'),
                'name'  => 'platform',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'customer',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('Customer Id'),
                'name'  => 'customer',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'manufacturer',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('Manufacturer'),
                'name'  => 'manufacturer',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'version',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('Version'),
                'name'  => 'version',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'device_model',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('Device Model'),
                'name'  => 'device_model',

           )
        );

        $fieldset->addField(
            'app_version',
            'text',
            array(
                'label' => Mage::helper('xcentia_mobile')->__('App Version'),
                'name'  => 'app_version',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('xcentia_mobile')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('xcentia_mobile')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('xcentia_mobile')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_device')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getDeviceData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getDeviceData());
            Mage::getSingleton('adminhtml/session')->setDeviceData(null);
        } elseif (Mage::registry('current_device')) {
            $formValues = array_merge($formValues, Mage::registry('current_device')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
