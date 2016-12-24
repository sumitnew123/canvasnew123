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
 * Vendor edit form tab
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Adminhtml_Vendor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Adminhtml_Vendor_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('vendor_');
        $form->setFieldNameSuffix('vendor');
        $this->setForm($form);
        
        $vendor = Mage::registry('current_vendor');
        if($vendor->getId() == 0) {
	        $fieldsets = $form->addFieldset(
	            'vendor_form_customer',
	            array('legend' => Mage::helper('xcentia_vendors')->__('Login Details'))
	        );
	        $fieldsets->addField(
	            'firstname',
	            'text',
	            array(
	                'label' => Mage::helper('xcentia_vendors')->__('Firstname'),
	                'name'  => 'firstname',
	                'required'  => true,
	                'class' => 'required-entry',
	
	           )
	        );
	        $fieldsets->addField(
	            'lastname',
	            'text',
	            array(
	                'label' => Mage::helper('xcentia_vendors')->__('Lastname'),
	                'name'  => 'lastname',
	                'required'  => true,
	                'class' => 'required-entry',
	
	           )
	        );
	        $fieldsets->addField(
	            'email',
	            'text',
	            array(
	                'label' => Mage::helper('xcentia_vendors')->__('Email'),
	                'name'  => 'email',
	                'required'  => true,
	                'class' => 'required-entry email',
	
	           )
	        );
	        $fieldsets->addField(
	            'dob',
	            'date',
	            array(
	                'label' => Mage::helper('xcentia_vendors')->__('Date of Birth'),
	                'name'  => 'dob',
					'image' => $this->getSkinUrl('images/grid-cal.gif'),
    				'format'=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
    				'value' => date( Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                                  strtotime('next weekday') )
	           )
	        );
	        $fieldsets->addField(
	            'mother_name',
	            'text',
	            array(
	                'label' => Mage::helper('xcentia_vendors')->__('Mothers maiden Name'),
	                'name'  => 'mother_name',	
	           )
	        );
        }
        
        $fieldset = $form->addFieldset(
            'vendor_form',
            array('legend' => Mage::helper('xcentia_vendors')->__('Vendor'))
        );
        $fieldset->addType(
            'image',
            Mage::getConfig()->getBlockClassName('xcentia_vendors/adminhtml_vendor_helper_image')
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
            'tagline',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Tagline'),
                'name'  => 'tagline',

           )
        );

        $fieldset->addField(
            'logo',
            'image',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Logo'),
                'name'  => 'logo',

           )
        );

        $fieldset->addField(
            'cover',
            'image',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Cover Photo'),
                'name'  => 'cover',

           )
        );

        $fieldset->addField(
            'about',
            'textarea',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('About'),
                'name'  => 'about',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'phone',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Phone'),
                'name'  => 'phone',

           )
        );

        $fieldset->addField(
            'address1',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Address Line 1'),
                'name'  => 'address1',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'address2',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Address Line 2'),
                'name'  => 'address2',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'landmark',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Landmark'),
                'name'  => 'landmark',
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
            'defaultValue'  => 'IN',
           )
        );

        $fieldset->addField(
            'state',
            'select',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('State'),
                'name'  => 'state',
                'required'  => true,
                'class' => 'required-entry',
				
           )
        );
        
        $fieldset->addField(
            'region_id',
            'hidden',
            array(
                'name'  => 'region_id',
           )
        );
		
    	$regionElement = $form->getElement('state');
        if ($regionElement) {
            $isRequired = Mage::helper('directory')->isRegionRequired('IN');
            $regionElement->setRequired($isRequired);
            $regionElement->setRenderer(Mage::getModel('xcentia_vendors/renderer_region'));
        }

    	$regionElement = $form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }
        
        
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
            'zip',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Zip/Postcode'),
                'name'  => 'zip',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        
        $fieldset->addField(
            'latitude',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Latitude'),
                'name'  => 'latitude',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'longitude',
            'text',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Longitude'),
                'name'  => 'longitude',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        
        /*
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
        */
        $fieldset->addField(
            'allow_comment',
            'select',
            array(
                'label' => Mage::helper('xcentia_vendors')->__('Allow Comments'),
                'name'  => 'allow_comment',
                'values'=> Mage::getModel('xcentia_vendors/adminhtml_source_yesnodefault')->toOptionArray()
            )
        );
        $formValues = Mage::registry('current_vendor')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getVendorData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getVendorData());
            Mage::getSingleton('adminhtml/session')->setVendorData(null);
        } elseif (Mage::registry('current_vendor')) {
            $formValues = array_merge($formValues, Mage::registry('current_vendor')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
