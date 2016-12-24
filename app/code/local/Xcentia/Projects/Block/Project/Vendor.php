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
class Xcentia_Projects_Block_Project_Vendor extends Mage_Core_Block_Template
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
                         //->addFieldToFilter('owner_id', Mage::helper('xcentia_vendors')->getDealer()->getId() )
                         ->addFieldToFilter('main_table.status', 1 );
        $projects->join(array('bids'=> 'xcentia_projects/bid'),'bids.project_id=main_table.entity_id', array('amount'=>'amount'), null,'left');
        $projects->addFieldToFilter('vendor_id', Mage::helper('xcentia_vendors')->getDealer()->getId() );
        $projects->setOrder('name', 'asc');
        $this->setProjects($projects);
    }
    
    public function getPastProjects() {
    	$projects = Mage::getResourceModel('xcentia_projects/project_collection')
                         //->addFieldToFilter('owner_id', Mage::helper('customer')->getCustomer()->getId() )
                         ->addFieldToFilter('main_table.status', array('neq' => 1) );
        $projects->join(array('bids'=> 'xcentia_projects/bid'),'bids.project_id=main_table.entity_id', array('amount'=>'amount'), null,'left');
        $projects->addFieldToFilter('vendor_id', Mage::helper('xcentia_vendors')->getDealer()->getId() );
        $projects->setOrder('name', 'asc');
        return $projects;
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
}
