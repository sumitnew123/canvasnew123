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
 * Bid edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Adminhtml_Bid_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Projects_Block_Adminhtml_Bid_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('bid_');
        $form->setFieldNameSuffix('bid');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'bid_form',
            array('legend' => Mage::helper('xcentia_projects')->__('Bid'))
        );
        $values = Mage::getResourceModel('xcentia_projects/project_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="bid_project_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeProjectIdLink() {
                if ($(\'bid_project_id\').value == \'\') {
                    $(\'bid_project_id_link\').hide();
                } else {
                    $(\'bid_project_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/projects_project/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'bid_project_id\').value);
                    $(\'bid_project_id_link\').href = realUrl;
                    $(\'bid_project_id_link\').innerHTML = text.replace(\'{#name}\', $(\'bid_project_id\').options[$(\'bid_project_id\').selectedIndex].innerHTML);
                }
            }
            $(\'bid_project_id\').observe(\'change\', changeProjectIdLink);
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
            'amount',
            'text',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Bid Amount'),
                'name'  => 'amount',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'date',
            'date',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Completion Date'),
                'name'  => 'date',
                'required'  => true,
                'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
           )
        );

        $fieldset->addField(
            'comments',
            'textarea',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Additional Comments'),
                'name'  => 'comments',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'selected',
            'select',
            array(
                'label' => Mage::helper('xcentia_projects')->__('Is Selected'),
                'name'  => 'selected',
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
        $formValues = Mage::registry('current_bid')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getBidData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getBidData());
            Mage::getSingleton('adminhtml/session')->setBidData(null);
        } elseif (Mage::registry('current_bid')) {
            $formValues = array_merge($formValues, Mage::registry('current_bid')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
