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
 * Vendor list block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Vendor_List extends Mage_Core_Block_Template
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
        $vendors = Mage::getResourceModel('xcentia_vendors/vendor_collection')
                         ->addFieldToFilter('status', 1);
        //$vendors->setOrder('title', 'asc');
        $cat = $this->getRequest()->getParam('cat', 0);
        if($cat > 0) {
        	$subquery = new Zend_Db_Expr('SELECT distinct vendor_id FROM xcentia_vendors_vendorservice vvs LEFT JOIN xcentia_vendors_service_category vsc ON vvs.service_id=vsc.service_id WHERE vsc.category_id = '.$cat);
			$vendors->addFieldToFilter('entity_id', array('in'=>$subquery));
        }
        $vendors->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $vendors->load();
        $this->setVendors($vendors);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Vendor_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_vendors.vendor.html.pager'
        )
        ->setCollection($this->getVendors());
        $this->setChild('pager', $pager);
        $this->getVendors()->load();
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
