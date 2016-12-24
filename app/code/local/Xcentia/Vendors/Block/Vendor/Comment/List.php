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
 * Vendor comment list block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Vendor_Comment_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $vendor = $this->getVendor();
        $comments = Mage::getResourceModel('xcentia_vendors/vendor_comment_collection')
             //->addFieldToSelect(array('AVG(rating)'))
             ->addFieldToFilter('vendor_id', $vendor->getId())
             ->addFieldToFilter('status', 1);
        $comments->setOrder('created_at', 'desc');
        $this->setComments($comments);
    }
	
    public function getAverageRating() {
    	$vendor = $this->getVendor();
        $comments = Mage::getResourceModel('xcentia_vendors/vendor_comment_collection')
             ->addFieldToFilter('vendor_id', $vendor->getId())
             ->addFieldToFilter('status', 1);
    	$comments->addFieldToSelect(array('rating'))->getSelect()->columns('AVG(rating) AS average');
        $comments->load();
        $vendor->setAverageRating($comments->getFirstItem()->getAverage())->save();
        return $comments->getFirstItem()->getAverage();
    }
    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Vendor_Comment_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_vendors.vendor.html.pager'
        )
        ->setCollection($this->getComments());
        $this->setChild('pager', $pager);
        $this->getComments()->load();
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
    /**
     * get the current vendor
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Vendor
     * @author Ultimate Module Creator
     */
    public function getVendor()
    {
        return Mage::registry('current_vendor');
    }
}
