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
 * Attachment edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Attachment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Attachment_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('attachment_');
        $form->setFieldNameSuffix('attachment');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'attachment_form',
            array('legend' => Mage::helper('xcentia_projects')->__('Attachment'))
        );
        $values = Mage::getResourceModel('xcentia_projects/message_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="attachment_message_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeMessageIdLink() {
                if ($(\'attachment_message_id\').value == \'\') {
                    $(\'attachment_message_id_link\').hide();
                } else {
                    $(\'attachment_message_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/projects_message/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'attachment_message_id\').value);
                    $(\'attachment_message_id_link\').href = realUrl;
                    $(\'attachment_message_id_link\').innerHTML = text.replace(\'{#name}\', $(\'attachment_message_id\').options[$(\'attachment_message_id\').selectedIndex].innerHTML);
                }
            }
            $(\'attachment_message_id\').observe(\'change\', changeMessageIdLink);
            changeMessageIdLink();
            </script>';

        $fieldset->addField(
            'message_id',
            'select',
            array(
                'label'     => Mage::helper('xcentia_projects')->__('Message'),
                'name'      => 'message_id',
                'required'  => false,
                'values'    => $values,
                'after_element_html' => $html
            )
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Name'),
                'name'  => 'name',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'filename',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Filename'),
                'name'  => 'filename',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'type',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Type'),
                'name'  => 'type',
                'required'  => true,
                'class' => 'required-entry',

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
        $formValues = Mage::registry('current_attachment')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getAttachmentData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getAttachmentData());
            Mage::getSingleton('adminhtml/session')->setAttachmentData(null);
        } elseif (Mage::registry('current_attachment')) {
            $formValues = array_merge($formValues, Mage::registry('current_attachment')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
