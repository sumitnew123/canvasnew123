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
 * Message edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Message_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Message_Edit_Tab_Form
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
            array('legend' => Mage::helper('xcentia_projects')->__('Message'))
        );
        $values = Mage::getResourceModel('xcentia_projects/project_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="message_project_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeProjectIdLink() {
                if ($(\'message_project_id\').value == \'\') {
                    $(\'message_project_id_link\').hide();
                } else {
                    $(\'message_project_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/projects_project/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'message_project_id\').value);
                    $(\'message_project_id_link\').href = realUrl;
                    $(\'message_project_id_link\').innerHTML = text.replace(\'{#name}\', $(\'message_project_id\').options[$(\'message_project_id\').selectedIndex].innerHTML);
                }
            }
            $(\'message_project_id\').observe(\'change\', changeProjectIdLink);
            changeProjectIdLink();
            </script>';

        $fieldset->addField(
            'project_id',
            'select',
            array(
                'label'     => Mage::helper('xcentia_projects')->__('Project'),
                'name'      => 'project_id',
                'required'  => false,
                'values'    => $values,
                'after_element_html' => $html
            )
        );

        $fieldset->addField(
            'message',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Message'),
                'name'  => 'message',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'sender_id',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Sender'),
                'name'  => 'sender_id',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'has_attachment',
            'select',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Has Attachment'),
                'name'  => 'has_attachment',
                'required'  => true,
                'class' => 'required-entry',

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
