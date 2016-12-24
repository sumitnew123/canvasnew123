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
 * Order list block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Order_Dashboard extends Mage_Core_Block_Template
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
        $vendors = Mage::getResourceModel('xcentia_vendors/order_collection')
                         ->addFieldToFilter('status', 1)
                         ->addFieldToFilter('vendor_id', Mage::helper('xcentia_vendors')->getDealer()->getId());
        $vendor_array = array();
        foreach($vendors as $vendor) {
        	$vendor_array[] = $vendor->getOrderId();
        }
        
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', array('neq' =>'complete'))
            ->addFieldToFilter('entity_id', array('in' => $vendor_array))
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
            ->setPageSize(5)
            ->setCurPage(1);
        $this->setOrders($orders);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Order_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /*$pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_vendors.order.html.pager'
        )
        ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();*/
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
    
	public function getOrderItems($order) {
        $items = Mage::getModel('sales/order_item')
                    ->getCollection()
                    ->addFieldToFilter('order_id', $order->getId());
        return $items;
    }
    
	public function getViewUrl($order)
    {
        return $this->getUrl('*/order/view', array('order_id' => $order->getId()));
    }
}
