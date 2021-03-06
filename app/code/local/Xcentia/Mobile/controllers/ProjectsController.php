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
class Xcentia_Mobile_ProjectsController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() {
		$projects = array();
		$page = $this->getRequest()->getParam('page', 1);
		
		$projectscolection = Mage::getResourceModel('xcentia_projects/project_collection')
                         ->addFieldToFilter('is_live', 1)
                         ;
        $cat = $this->getRequest()->getParam('cat', 0);
        if($cat > 0) {
        	$ids = array();
        	$catgory = Mage::getModel('catalog/category')->load($cat);
        	$collections = Mage::getResourceModel('catalog/product_collection')
	        				->addCategoryFilter($catgory);
	        foreach($collections as $product ) $ids[] = $product->getId();
	        $projectscolection->addFieldToFilter('product_id', array('in'=>$ids));
        }
        $projectscolection->setPageSize(10)
            			 ->setCurPage($page)
                         ->setOrder('entity_id','DESC')
                         ;
        
        foreach($projectscolection as $project) {
        	$projects[] = (object)array('id' => $project->getId(),
        							'name' => $project->getName(),
        							'budget' => Mage::helper('core')->currency($project->getBudget(), true, false),
        							'lowestbid' => ($project->getLowestBid()) ? Mage::helper('core')->currency($project->getLowestBid(), true, false) : 'no bids yet',
        							'bid_end' => Mage::helper('core')->formatDate($project->getBidEnd(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false),
        							'image' => (String)Mage::helper('xcentia_projects')->getProjectImagePng($project),
        							);
        }
        if(($page-1)*10 < $projectscolection->getSize()) {
        	$response = array('total' => $projectscolection->getSize(), 'page' => (int)$page, 'items' => $projects);
        } else {
        	$response = array('total' => $projectscolection->getSize(), 'page' => (int)$page, 'items' => array());
        }
        
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($response));
	}
	
	public function viewAction() {
		$projectId  = (int) $this->getRequest()->getParam('id');
		$customerId  = (int) $this->getRequest()->getParam('customer');
		$project    = Mage::getModel('xcentia_projects/project')->load($projectId);
		$dealer = Mage::getModel('xcentia_vendors/vendor')->load($customerId, 'customer_id');
		
		$projectinfo = new stdClass();
		$projectinfo->name = $project->getName();
		$projectinfo->image = Mage::helper('xcentia_projects')->getProjectImagePng($project);
		$projectinfo->description = json_decode($project->getOptions());
		$projectinfo->budget =  Mage::helper('core')->currency($project->getBudget(), true, false);
        $projectinfo->lowestbid = ($project->getLowestBid()) ? Mage::helper('core')->currency($project->getLowestBid(), true, false) : 'no bids yet';
        $projectinfo->bid_end = Mage::helper('core')->formatDate($project->getBidEnd(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false);
		
		$diff=date_diff(date_create(date("Y-m-d")),date_create($project->getBidEnd()));
        	if($diff->format("%R") == '-') {
				$projectinfo->days_left =  '<div class="size-30">Closed</div>';
        	} else if($diff->format("%R%m") > 0) {
  				$projectinfo->days_left = '<div class="size-30">'.$diff->format("%m") . ' Months</div>';
			} else if($diff->format("%R%d") > 0) {
				$projectinfo->days_left = '<div class="size-30">'.$diff->format("%d") . ' Days</div>';
			} else if($diff->format("%R%h") > 0) {
				$projectinfo->days_left = '<div class="size-30">'.$diff->format("%h") . ' Hours</div>';
			} else if($diff->format("%R%i") > 0) {
				$projectinfo->days_left = '<div class="size-30">'.$diff->format("%i") . ' Minutes</div>';
			}
			
        $projectinfo->total_bids = $project->getTotalBids();
		$projectinfo->id = $project->getId();

		$projectinfo->dealer_id = $dealer->getId();
		$projectinfo->can_bid = Mage::helper('xcentia_vendors/vendor')->canBidonProject($project, $dealer);
		$projectinfo->already_bid = Mage::helper('xcentia_projects/project')->hasAlreadyBidByVendor($project, $dealer);
		
        $this->getResponse()->clearHeaders()->setHeader('Access-Control-Allow-Origin','*',true)->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($projectinfo));
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