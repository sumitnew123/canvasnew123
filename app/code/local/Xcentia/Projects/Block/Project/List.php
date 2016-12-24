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
 * Project list block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author Ultimate Module Creator
 */
class Xcentia_Projects_Block_Project_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $projects = Mage::getResourceModel('xcentia_projects/project_collection')
                         ->addFieldToFilter('is_live', 1)
                         ;
        $cat = $this->getRequest()->getParam('cat', 0);
        if($cat > 0) {
        	$ids = array();
        	$catgory = Mage::getModel('catalog/category')->load($cat);
        	$collections = Mage::getResourceModel('catalog/product_collection')
	        				->addCategoryFilter($catgory);
	        foreach($collections as $product ) $ids[] = $product->getId();
	        $projects->addFieldToFilter('product_id', array('in'=>$ids));
        }
        $projects->setOrder('entity_id', 'desc');
        $projects->setPageSize(10)->setCurPage(1)->load();
        $this->setProjects($projects);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Projects_Block_Project_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_projects.project.html.pager'
        )
        ->setCollection($this->getProjects());
        $this->setChild('pager', $pager);
        $this->getProjects()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getProjectImage($project) {
    	if($project->getType() == 'design') {
    		$design = Mage::getModel('pdp/customerdesign')->load($project->getDesignId());
    		$thumbnails = Mage::helper("pdp")->getThumbnailImage($design->getFilename());
    		return $thumbnails[0]['image'];
    	} else if($project->getType() == 'travel') {
    		$options = json_decode($project->getOptions());
    		//return '<img src="'.$this->getSkinUrl('images/map.png').'" class="img-responsive">';
    		return '<img src="https://maps.googleapis.com/maps/api/staticmap?size=200x200&maptype=roadmap&markers=color:green|label:O|'.$options->Origin.'&markers=color:red|label:D|'.$options->Destination.'&path=color:blue|weight:5|'.$options->Origin.'|'.$options->Destination.'&key=AIzaSyBSOCQP0Dj3gMLF7umj7-x7FWeolQyBnIs" class="img-responsive">';
    		
    	}
    }
}
