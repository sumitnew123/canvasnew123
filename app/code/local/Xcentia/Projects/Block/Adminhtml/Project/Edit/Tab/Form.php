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
 * Project edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Project_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Project_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('project_');
        $form->setFieldNameSuffix('project');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'project_form',
            array('legend' => Mage::helper('xcentia_projects')->__('Project'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Project Name'),
                'name'  => 'name',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'quantity',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Quantity'),
                'name'  => 'quantity',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'budget',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Budget (per piece)'),
                'name'  => 'budget',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'expected',
            'date',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Expected Time'),
                'name'  => 'expected',
                'required'  => true,
                'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
           )
        );

        $fieldset->addField(
            'options',
            'textarea',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Options'),
                'name'  => 'options',

           )
        );

        $fieldset->addField(
            'type',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Project Type'),
                'name'  => 'type',

           )
        );

        $fieldset->addField(
            'design_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Design'),
                'name'  => 'design_id',

           )
        );

        $fieldset->addField(
            'owner_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Owner'),
                'name'  => 'owner_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'is_private',
            'select',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Is Private'),
                'name'  => 'is_private',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('xcentia_projects')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('xcentia_projects')->__('No'),
                ),
            ),
           )
        );

        $fieldset->addField(
            'is_single',
            'select',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Is Single'),
                'name'  => 'is_single',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('xcentia_projects')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('xcentia_projects')->__('No'),
                ),
            ),
           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('xcentia_projects')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('xcentia_projects')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('xcentia_projects')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_project')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getProjectData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getProjectData());
            Mage::getSingleton('adminhtml/session')->setProjectData(null);
        } elseif (Mage::registry('current_project')) {
            $formValues = array_merge($formValues, Mage::registry('current_project')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
