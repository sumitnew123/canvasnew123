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
 * Order edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Order_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Order_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('order_');
        $form->setFieldNameSuffix('order');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'order_form',
            array('legend' => Mage::helper('xcentia_vendors')->__('Order'))
        );
        $values = Mage::getResourceModel('xcentia_vendors/vendor_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="order_vendor_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeVendorIdLink() {
                if ($(\'order_vendor_id\').value == \'\') {
                    $(\'order_vendor_id_link\').hide();
                } else {
                    $(\'order_vendor_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/vendors_vendor/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'order_vendor_id\').value);
                    $(\'order_vendor_id_link\').href = realUrl;
                    $(\'order_vendor_id_link\').innerHTML = text.replace(\'{#name}\', $(\'order_vendor_id\').options[$(\'order_vendor_id\').selectedIndex].innerHTML);
                }
            }
            $(\'order_vendor_id\').observe(\'change\', changeVendorIdLink);
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
            'order_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Order Id'),
                'name'  => 'order_id',
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
        $formValues = Mage::registry('current_order')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getOrderData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getOrderData());
            Mage::getSingleton('adminhtml/session')->setOrderData(null);
        } elseif (Mage::registry('current_order')) {
            $formValues = array_merge($formValues, Mage::registry('current_order')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
