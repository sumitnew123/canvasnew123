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
 * Coverage edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Coverage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Coverage_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('coverage_');
        $form->setFieldNameSuffix('coverage');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'coverage_form',
            array('legend' => Mage::helper('xcentia_vendors')->__('Coverage'))
        );
        $values = Mage::getResourceModel('xcentia_vendors/vendor_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="coverage_vendor_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeVendorIdLink() {
                if ($(\'coverage_vendor_id\').value == \'\') {
                    $(\'coverage_vendor_id_link\').hide();
                } else {
                    $(\'coverage_vendor_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/vendors_vendor/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'coverage_vendor_id\').value);
                    $(\'coverage_vendor_id_link\').href = realUrl;
                    $(\'coverage_vendor_id_link\').innerHTML = text.replace(\'{#name}\', $(\'coverage_vendor_id\').options[$(\'coverage_vendor_id\').selectedIndex].innerHTML);
                }
            }
            $(\'coverage_vendor_id\').observe(\'change\', changeVendorIdLink);
            changeVendorIdLink();
            </script>';

        $fieldset->addField(
            'vendor_id',
            'select',
            array(
                'label'     => Mage::helper('xcentia_vendors')->__('Vendor'),
                'name'      => 'vendor_id',
                'required'  => false,
                'values'    => $values,
                'after_element_html' => $html
            )
        );

        $fieldset->addField(
            'city',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('City'),
                'name'  => 'city',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'state',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('State'),
                'name'  => 'state',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'country',
            'select',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Country'),
                'name'  => 'country',
                'required'  => true,
                'class' => 'required-entry',

            'values'=> Mage::getResourceModel('directory/country_collection')->toOptionArray(),
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
        $formValues = Mage::registry('current_coverage')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getCoverageData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getCoverageData());
            Mage::getSingleton('adminhtml/session')->setCoverageData(null);
        } elseif (Mage::registry('current_coverage')) {
            $formValues = array_merge($formValues, Mage::registry('current_coverage')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
