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
 * Servicemap edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Servicemap_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Servicemap_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('servicemap_');
        $form->setFieldNameSuffix('servicemap');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'servicemap_form',
            array('legend' => Mage::helper('xcentia_vendors')->__('Servicemap'))
        );

        $fieldset->addField(
            'vendor_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Vendor'),
                'name'  => 'vendor_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'service_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Service'),
                'name'  => 'service_id',
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
        $formValues = Mage::registry('current_servicemap')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getServicemapData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getServicemapData());
            Mage::getSingleton('adminhtml/session')->setServicemapData(null);
        } elseif (Mage::registry('current_servicemap')) {
            $formValues = array_merge($formValues, Mage::registry('current_servicemap')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
