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
 * Project front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_CraftsmenController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() {
		$page = $this->getRequest()->getParam('page', 1);
		$vendors = Mage::getResourceModel('xcentia_vendors/vendor_collection')
                         ->addFieldToFilter('status', 1);
        //$vendors->setOrder('title', 'asc');
        $cat = $this->getRequest()->getParam('cat', 0);
        if($cat > 0) {
        	$subquery = new Zend_Db_Expr('SELECT distinct vendor_id FROM xcentia_vendors_vendorservice vvs LEFT JOIN xcentia_vendors_service_category vsc ON vvs.service_id=vsc.service_id WHERE vsc.category_id = '.$cat);
			$vendors->addFieldToFilter('entity_id', array('in'=>$subquery));
        }
        //$vendors->getSelect()->order(new Zend_Db_Expr('RAND()'));

        $vendors->setPageSize(10)
         		->setCurPage($page);
        
        $vendors->load();
        
		foreach($vendors as $vendor) {
        	$vendorarray[] = (object)array('id' => $vendor->getId(),
        							'name' => $vendor->getTitle(),
        							'about' => Mage::helper('core/string')->truncate($vendor->getAbout(), 200),
        							'prodcount' => $vendor->getProductCount(),
        							'logo' => (String)Mage::helper('xcentia_vendors/vendor_image')->init($vendor, 'logo'),
        							'featured' => $vendor->getFeaturedProducts(),
        							'rating' => $vendor->getAverageRating(),
        							);
        }

        if(($page-1)*10 < $vendors->getSize()) {
        	$response = array('total' => $vendors->getSize(), 'page' => (int)$page, 'items' => $vendorarray);
        } else {
        	$response = array('total' => $vendors->getSize(), 'page' => (int)$page, 'items' => array());
        }
        
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function viewAction() {
		$vendorId  = (int) $this->getRequest()->getParam('id');
		$vendor =  Mage::getModel('xcentia_vendors/vendor')->load($vendorId);
		
		$vendorinfo = new stdClass();
		$vendorinfo->name = $vendor->getName();
		$vendorinfo->logo = (String)Mage::helper('xcentia_vendors/vendor_image')->init($vendor, 'logo');
		$vendorinfo->cover = (String)Mage::helper('xcentia_vendors/vendor_image')->init($vendor, 'cover');
		$vendorinfo->name = $vendor->getTitle();
		$vendorinfo->about = strip_tags($vendor->getAbout());
		
		$vendorinfo->email = $vendor->getEmail();
		$vendorinfo->phone = $vendor->getPhone();
		$vendorinfo->address1 = $vendor->getAddress1();
		$vendorinfo->address2 = $vendor->getAddress2();
		$vendorinfo->city = $vendor->getCity();
		$vendorinfo->prodcount = $vendor->getProductCount();
		$vendorinfo->id = $vendor->getId();
		
		$vendorinfo->rating = $vendor->getAverageRating();
		$vendorinfo->stars = round($vendor->getAverageRating());
		
		$services = Mage::getModel('xcentia_vendors/vendorservice')
					->getCollection()
					->addFieldToFilter('vendor_id', $vendor->getId());
		foreach($services as $service)
			$sers[] = $service->getServiceId();
			
		$cats = Mage::getModel('xcentia_vendors/service')
				->getCollection()
				->addFieldToFilter('entity_id', array('in'=>$sers));
		foreach($cats as $cat)
			$vendorinfo->services[] = $cat->getTitle();
			
		$comments = Mage::getResourceModel('xcentia_vendors/vendor_comment_collection')
             //->addFieldToSelect(array('AVG(rating)'))
             ->addFieldToFilter('vendor_id', $vendor->getId())
             ->addFieldToFilter('status', 1);
        $comments->setOrder('created_at', 'desc');
				
        foreach($comments as $comment)
			$vendorinfo->reveiws[] = array('rating'=> (int)$comment->getrating(), 'title'=>$comment->getTitle(), 'comment'=>$comment->getComment(), 'name'=>$comment->getName(), 'dateofreview' => Mage::helper('core')->formatDate($comment->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false) );
        
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($vendorinfo));
	}
	
	public function categoriesAction() {
		$rootcategory = Mage::getModel('catalog/category')->load(28);
    	$categoryCollection= $rootcategory->getChildrenCategories();
		$categoryCollection->addIsActiveFilter();
		$categoryCollection->load();
		$categories = array();
		$categories[28]['name'] = 'All';
		$categories[28]['id'] = 28;
		foreach ($categoryCollection as $category) {
			$categories[$category->getId()]['name'] = $category->getName();
			$categories[$category->getId()]['id'] = $category->getId();
			$subcats = Mage::getResourceModel('catalog/category_collection')
		                     ->addAttributeToSelect('name')
		                     ->addAttributeToFilter('entity_id', explode(',', $category->getChildren()))
		                     ->addIsActiveFilter();
			foreach($subcats as $subcat) {
				$categories[$category->getId()]['subcats'][$subcat->getId()]['name'] = $subcat->getName();
				$categories[$category->getId()]['subcats'][$subcat->getId()]['id'] = $subcat->getId();
            }                 
	
		}
		$this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($categories));
	}
}