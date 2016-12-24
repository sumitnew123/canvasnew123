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
 * Service edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Service_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Service_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('service_');
        $form->setFieldNameSuffix('service');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'service_form',
            array('legend' => Mage::helper('xcentia_vendors')->__('Service'))
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Title'),
                'name'  => 'title',
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
        $formValues = Mage::registry('current_service')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getServiceData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getServiceData());
            Mage::getSingleton('adminhtml/session')->setServiceData(null);
        } elseif (Mage::registry('current_service')) {
            $formValues = array_merge($formValues, Mage::registry('current_service')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
