<?php 
/**
 * Wa_Dealer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Wa
 * @package		Wa_Dealer
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Page view block
 *
 * @category	Wa
 * @package		Wa_Dealer
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Products_Specifications extends Mage_Core_Block_Template{
	/**
	 * get the current page
	 * @access public
	 * @return mixed (Wa_Dealer_Model_Page|null)
	 * @author Ultimate Module Creator
	 */
	public function getAttributes(){
		try {
			$table_eav_entity_attribute = Mage::getSingleton("core/resource")->getTableName('eav_entity_attribute');
			$table_eav_attribute_group = Mage::getSingleton("core/resource")->getTableName('eav_attribute_group');
			
			$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
					->setEntityTypeFilter('4');
			$attributes->getSelect()->join( array('entity_attribute' => $table_eav_entity_attribute), 'main_table.attribute_id = entity_attribute.attribute_id', array('attribute_set_id'))
									->join( array('ag' => $table_eav_attribute_group), 'entity_attribute.attribute_group_id = ag.attribute_group_id', array());
			$attributes->addFieldToFilter('attribute_group_name','Specification')
						->addFieldToFilter('entity_attribute.attribute_set_id', Mage::registry('new_product_attribute_set'));
			return $attributes;
		} catch(Exception $e) {
			return '';
		}
	}
	
	public function getProduct(){
		return Mage::registry('current_dealer_product');
	}
	
	public function getInputField($attribute,$product) {
		$product = $this->getProduct();
		$attribute_val = $product->getData($attribute->getAttributeCode());
		
		$html = '';
		switch($attribute->getFrontendInput()) {
			case 'select':
				if($attribute->getSourceModel() != '') {
					$options = Mage::getModel($attribute->getSourceModel())
								->getAllOptions();
					$html = '<select name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'">';
					//$html .= '<option value="">-- SELECT --</option>';
					foreach($options as $option)
						$html .= '<option value="'.$option['value'].'"'.(($attribute_val==$option['value'])?' selected="selected"':'').'>'.$option['label'].'</option>';
					$html .= '</select>';
				} else {
					$options = Mage::getResourceModel('eav/entity_attribute_option_collection')
								->setAttributeFilter($attribute->getAttributeId())
								->setStoreFilter(1)
								->toOptionArray();
					$html = '<select name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'">';
					$html .= '<option value="">-- SELECT --</option>';
					foreach($options as $option)
						$html .= '<option value="'.$option['value'].'"'.(($attribute_val==$option['value'])?' selected="selected"':'').'>'.$option['label'].'</option>';
					$html .= '</select>';
				}
			break;
			case 'textarea':
				$html = '<textarea name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'">'.$attribute_val.'</textarea>';
			break;
			case 'boolean':
				$html = '<select name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'">';
				$html .= '<option value="0"'.(($attribute_val==0)?' selected="selected"':'').'>No</option><option value="1"'.(($attribute_val==1)?' selected="selected"':'').'>Yes</option>';
				$html .= '</select>';
			break;
			case 'multiselect':
				if(($attribute->getAttributeCode() == 'color') || ($attribute->getAttributeCode() == 'material_filter')){
					$attribute_val = str_replace(',',";",$product->getResource()->getAttribute($attribute->getAttributeCode())->getFrontend()->getValue($product));
					$html = '<input type="text" value="'.$attribute_val.'" name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'" />';
				}else{
					$html = '<input type="text" value="'.$attribute_val.'" name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'" />';
				}
				
			break;
			case 'text':
			default:
				$html = '<input type="text" value="'.$attribute_val.'" name="'.$attribute->getAttributeCode().'" id="'.$attribute->getAttributeCode().'" class="form-control'.($attribute->getIsRequired() ? ' required-entry':'' ).'" />';
			break;
			
		}
		return $html;
	}
} 