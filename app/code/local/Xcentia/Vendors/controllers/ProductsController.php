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
 * Page front contrller
 *
 * @category	Wa
 * @package		Wa_Dealer
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_ProductsController extends Mage_Core_Controller_Front_Action{
	
/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!$this->_getSession()->authenticate($this)) {
        	$this->setFlag('', 'no-dispatch', true);
        } else if(!((int)Mage::helper('xcentia_vendors')->getDealer()->getId() > 0 )) {
        	$this->_getSession()->addError(Mage::helper('xcentia_vendors')->__('Restricted Area. Entry prohibitted.'));
        	$this->_redirect('customer/account');
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
    
    
	/**
 	 * default action
 	 * @access public
 	 * @return void
 	 * @author Ultimate Module Creator
 	 */
 	public function indexAction(){
		$this->loadLayout();
 		$dealer = Mage::helper('xcentia_vendors')->getDealer();
 		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$headBlock->setTitle($dealer->getTitle().' : '.Mage::helper('xcentia_vendors')->__('Products and Services'));
			$headBlock->setKeywords(Mage::getStoreConfig('dealer/dealer/meta_keywords'));
			$headBlock->setDescription(Mage::getStoreConfig('dealer/dealer/meta_description'));
		}
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	public function newAction() {
		$this->_forward('selectset');
	}
	
	public function setselectedAction() {
		$attribute_set = $this->getRequest()->getParam('attribute_set_id', 0);
		Mage::register('new_product_attribute_set', $attribute_set);
		$this->_forward('edit');
	}
	
	public function selectsetAction(){
		$this->loadLayout();
		$dealer = Mage::helper('xcentia_vendors')->getDealer();
 		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$headBlock->setTitle($dealer->getBusinessName().' : '.Mage::helper('xcentia_vendors')->__('Select Accessory Type'));
			$headBlock->setKeywords(Mage::getStoreConfig('dealer/dealer/meta_keywords'));
			$headBlock->setDescription(Mage::getStoreConfig('dealer/dealer/meta_description'));
		}
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	public function deleteAction(){
		Mage::register('isSecureArea', true);
		$productId 	= $this->getRequest()->getParam('id', 0);
		$session = $this->_getSession();
		try{
			$product 	= Mage::getModel('catalog/product')
							->setStoreId(Mage::app()->getStore()->getId())
							->load($productId);
			if($product->getId() > 0) {
				$product->delete();
			}
			$session->addSuccess(Mage::helper('xcentia_vendors')->__('Product was successfully deleted'));
			$this->_redirect('*/*/');
		} catch (Exception $e) {
			$session->addError($e->getMessage());
			$session->addError(Mage::helper('xcentia_vendors')->__('There was a problem Deleting the Product.'));
		}
	}
	public function editAction(){
		$productId 	= $this->getRequest()->getParam('id', 0);
		$product 	= Mage::getModel('catalog/product')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($productId);
		$attribute_set = Mage::registry('new_product_attribute_set');
		if(!($attribute_set>0)) {
			Mage::register('new_product_attribute_set', $product->getAttributeSetId());
		}
		Mage::register('current_dealer_product', $product);
		$this->loadLayout();
		$dealer = Mage::helper('xcentia_vendors')->getDealer();
 		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$headBlock->setTitle($dealer->getBusinessName().' : '.Mage::helper('xcentia_vendors')->__('Add/Edit Accesories'));
			$headBlock->setKeywords(Mage::getStoreConfig('dealer/dealer/meta_keywords'));
			$headBlock->setDescription(Mage::getStoreConfig('dealer/dealer/meta_description'));
		}
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	public function saveAction() {
		$session = $this->_getSession();
		if ($data = $this->getRequest()->getPost()) {
			try {
				$productId  = $data['id'];
				$dealer = Mage::helper('xcentia_vendors')->getDealer();
				$store_id = Mage::app()->getStore()->getId();
				if ($productId > 0) {
					$product = Mage::getModel('catalog/product')->load($productId)
							    ->setCategoryIds($data['category_ids'])
							    ->setPrice($data['price'])
							    ->setWeight($data['weight'])
							    ->setShortDescription($data['short_description'])
							    ->setDescription($data['description'])
							    ->setName($data['name'])
							    ->setTaxClassId($data['tax_class_id'])
							    ->setStatus($data['status'])
							    ->setIsFeatured($data['is_featured'])
							    ->setStockData(array(
													'is_in_stock' =>$data['is_in_stock'],
													'qty' => (int)$data['qty']
												))
								->setMetaTitle($data['meta_title'])
								->setMetaKeyword($data['meta_keyword'])
								->setMetaDescription($data['meta_description'])
							    ->save();
				} else {
					$website_id = Mage::app()->getStore($store_id)->getWebsiteId();
					$sku = Mage::helper('xcentia_vendors')->createUniqueSku($data['name']);
					$product = Mage::getModel('catalog/product')
							    ->setStoreId($store_id)
							    ->setWebsiteIds(array($website_id))
							    ->setCategoryIds($data['category_ids'])
							    ->setAttributeSetId($data['type'])
							    ->setPrice($data['price'])
							    ->setWeight($data['weight'])
							    ->setShortDescription($data['short_description'])
							    ->setDescription($data['description'])
							    ->setSku($sku)
							    ->setName($data['name'])
							    ->setTaxClassId($data['tax_class_id'])
							    ->setStatus($data['status'])
							    ->setIsFeatured($data['is_featured'])
							    ->setTypeId('simple')
							    ->setStockData(array(
													'is_in_stock' =>$data['is_in_stock'],
													'qty' => (int)$data['qty']
												))
								->setMetaTitle($data['meta_title'])
								->setMetaKeyword($data['meta_keyword'])
								->setMetaDescription($data['meta_description'])
								//->setCreatorId($dealer->getCustomerId())
								->setDealerId($dealer->getId())
								->addData($data);
							    //->save();
				}
				
				if(sizeof($data['remove_image']) > 0) {
					$attributes = $product->getTypeInstance(true)->getSetAttributes($product);
			        if (isset($attributes['media_gallery'])) {
				        $mediaGalleryAttribute = $attributes['media_gallery'];
						foreach($data['remove_image'] as $file) {
							$mediaGalleryAttribute->getBackend()->removeImage($product, $file);
						}
			        }
				}

				$imgArray = explode(';', trim($data['images'], ';'));
				// We set up a $count variable - the first image gets used as small, thumbnail and base
    			if($data['images'] != '' and sizeof($imgArray) > 0) {
    				$count = 0;
	    			$mediaAttribute = array ('thumbnail','small_image','image');
				    foreach ($imgArray as $image) :
			            if ($count == 0) :
			                $product->addImageToMediaGallery(Mage::getBaseDir('media').DS.'import'.DS.$image , $mediaAttribute, false, false ); 
			            else :
			                $product->addImageToMediaGallery(Mage::getBaseDir('media').DS.'import'.DS.$image , null, false, false );
			            endif;
			            $count++;  
				    endforeach;
    			}
    			
    			//$color = $this->replaceOptions('color', $data['color']);
    			//$product->setColor($color);
    			//$material_filter = $this->replaceOptions('material_filter', $data['material_filter']);
    			//$product->setMaterialFilter($material_filter);
    			//$product->setSpecification($data['specification']);
    			$product->save();
				
				$session->addSuccess(Mage::helper('xcentia_vendors')->__('product was successfully saved'));
				$this->_redirect('*/*/');
				return;
			} 
			catch (Exception $e) {
				Mage::logException($e);
				$session->addError($e->getMessage());
				$session->addError(Mage::helper('xcentia_vendors')->__('There was a problem saving the product.'));
				$session->setFormData($data);
				if ($productId > 0) {
					$this->_redirect('*/*/edit', array('id' => $productId));
				} else {
					$this->_redirect('*/*/edit');
				}
				return;
			}
		}
		$session->addError(Mage::helper('xcentia_vendors')->__('Unable to find product to save.'));
		$this->_redirect('*/*/');
	}
	
	public function replaceOptions($attribute_type,$attributes_options){
		$attributes = explode(';',$attributes_options);
		$attributes_ids = array();
		foreach ($attributes as $_attribute){
			$optionValue = $this->getAttributeOptionValue($attribute_type, ucfirst(strtolower(trim($_attribute))));
			if(empty($optionValue)){
		  		$optionValue = $this->addAttributeOption($attribute_type, ucfirst(strtolower(trim($_attribute))));
			}
			$attributes_ids[]=$optionValue;
		}
		return $attributes_ids;
	}
	
	public function getAttributeOptionValue($arg_attribute, $arg_value) {
	    $attribute_model        = Mage::getModel('eav/entity_attribute');
	    $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
	 
	    $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
	    $attribute              = $attribute_model->load($attribute_code);
	 
	    $attribute_table        = $attribute_options_model->setAttribute($attribute);
	    $options                = $attribute_options_model->getAllOptions(false);
	 
	    foreach($options as $option) {
	        if ($option['label'] == $arg_value) {
	            return $option['value'];
	        }
	    }
	 
	    return false;
	}

	public function addAttributeOption($arg_attribute, $arg_value) {
	    $attribute_model        = Mage::getModel('eav/entity_attribute');
	    $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
	 
	    $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
	    $attribute              = $attribute_model->load($attribute_code);
	 
	    $attribute_table        = $attribute_options_model->setAttribute($attribute);
	    $options                = $attribute_options_model->getAllOptions(false);
	 
	    $value['option'] = array($arg_value,$arg_value);
	    $result = array('value' => $value);
	    $attribute->setData('option',$result);
	    $attribute->save();
	 
	    return $this->getAttributeOptionValue($arg_attribute, $arg_value);
	}

	public function iuploadAction() {
		 $_FILES['image'] = array('name' => $_FILES['image']['name'][0], 'type' => $_FILES['image']['type'][0], 'tmp_name' => $_FILES['image']['tmp_name'][0], 'error' => $_FILES['image']['error'][0], 'size' => $_FILES['image']['size'][0] );
			try {
				$uploader = new Varien_File_Uploader('image');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(false);
				$uploader->setAllowCreateFolders(true);
				$fileName = $_FILES['image']['name'];
				$newFileName = strtotime("now").'-'.rand(1,999).'.'.substr($fileName, strrpos($fileName, '.')+1);
				$result = $uploader->save( Mage::getBaseDir('media').'/import/', $newFileName);
				echo $result['file'];
				exit;
			} 
			catch (Exception $e) {
				echo $e->getMessage();
				exit;
			}
	}
	
	public function addservicesAction() {
		$Id 	= $this->getRequest()->getParam('id', 0);
		if($Id > 0) {
			$data = array('vendor_id'=>(int)Mage::helper('xcentia_vendors')->getDealer()->getId(), 'service_id' => $Id);
			$services = Mage::getModel('xcentia_vendors/vendorservice');
            $services->addData($data);
            $services->save();
		}
		exit;
	}
	public function removeservicesAction() {
		$Id 	= $this->getRequest()->getParam('id', 0);
		if($Id > 0) {
			$query = "DELETE FROM xcentia_vendors_vendorservice WHERE vendor_id = " . Mage::helper('xcentia_vendors')->getDealer()->getId() . " AND service_id = ". $Id;
			Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
		}
		exit;
	}
	
	public function featuredAction(){
		$productId 	= $this->getRequest()->getParam('id', 0);
		$make = $this->getRequest()->getParam('make', 'no');
		$product 	= Mage::getModel('catalog/product')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($productId);
		
		if($make == 'yes') {
			$product->setIsFeatured(1);
		} else {
			$product->setIsFeatured(0);
		}
		$product->save();
		$this->_redirect('*/*/');
	}
	
	public function testAction(){
		$vendors = Mage::getModel('xcentia_vendors/vendor')
					->getCollection()
					->addFieldToFilter('status', 1);
		$vendors->getSelect()->order("updated_at asc")->limit(5);
		foreach($vendors as $vendor) {
			$prodjson = array();
			$products = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSelect(array('small_image','is_featured'))
					->addAttributeToFilter('dealer_id', $vendor->getId());
			$count = $products->getSize();
			$products->addAttributeToFilter('is_featured', 1)
					->addFieldToFilter('type_id','simple')
					->addUrlRewrite()
					->setPageSize(3);
				foreach($products as $product) {
					$prodjson[] = array('url'=>$product->getProductUrl(), 'image'=>(string)Mage::helper('catalog/image')->init($product, 'small_image')->keepFrame(false)->resize(100));
				}
			$vendor->setProductCount($count);
			$vendor->setFeaturedProducts(json_encode($prodjson));
			$vendor->save();
		}
	}
}