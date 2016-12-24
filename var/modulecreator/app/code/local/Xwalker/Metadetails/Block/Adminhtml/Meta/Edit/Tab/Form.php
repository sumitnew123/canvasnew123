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
 * Meta edit form tab
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Block_Adminhtml_Meta_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xwalker_Metadetails_Block_Adminhtml_Meta_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('meta_');
        $form->setFieldNameSuffix('meta');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'meta_form',
            array('legend' => Mage::helper('xwalker_metadetails')->__('Meta'))
        );

        $fieldset->addField(
            'url',
            'text',
            array(
                'label' => Mage::helper('xwalker_metadetails')->__('URL'),
                'name'  => 'url',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('xwalker_metadetails')->__('TITLE'),
                'name'  => 'title',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'description',
            'textarea',
            array(
                'label' => Mage::helper('xwalker_metadetails')->__('DESCRIPTION'),
                'name'  => 'description',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'keywords',
            'textarea',
            array(
                'label' => Mage::helper('xwalker_metadetails')->__('KEYWORDS'),
                'name'  => 'keywords',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'h1tag',
            'text',
            array(
                'label' => Mage::helper('xwalker_metadetails')->__('H1 TAG'),
                'name'  => 'h1tag',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('xwalker_metadetails')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('xwalker_metadetails')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('xwalker_metadetails')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_meta')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getMetaData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getMetaData());
            Mage::getSingleton('adminhtml/session')->setMetaData(null);
        } elseif (Mage::registry('current_meta')) {
            $formValues = array_merge($formValues, Mage::registry('current_meta')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
