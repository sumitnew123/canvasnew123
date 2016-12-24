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
 * Order abstract REST API handler model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
abstract class Xcentia_Vendors_Model_Api2_Order_Rest extends Xcentia_Vendors_Model_Api2_Order
{
    /**
     * current order
     */
    protected $_order;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Ultimate Module Creator
     */
    protected function _retrieve() {
        $order = $this->_getOrder();
        $this->_prepareOrderForResponse($order);
        return $order->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('xcentia_vendors/order_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addFieldToFilter('status', array('eq' => 1));
        $this->_applyCollectionModifiers($collection);
        $orders = $collection->load();
        $orders->walk('afterLoad');
        foreach ($orders as $order) {
            $this->_setOrder($order);
            $this->_prepareOrderForResponse($order);
        }
        $ordersArray = $orders->toArray();
        $ordersArray = $ordersArray['items'];

        return $ordersArray;
    }

    /**
     * prepare order for response
     *
     * @access protected
     * @param Xcentia_Vendors_Model_Order $order
     * @author Ultimate Module Creator
     */
    protected function _prepareOrderForResponse(Xcentia_Vendors_Model_Order $order) {
        $orderData = $order->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $orderData['url'] = $order->getOrderUrl();
        }
    }

    /**
     * create order
     *
     * @access protected
     * @param array $data
     * @return string|void
     * @author Ultimate Module Creator
     */
    protected function _create(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * update order
     *
     * @access protected
     * @param array $data
     * @author Ultimate Module Creator
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete order
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current order
     *
     * @access protected
     * @param Xcentia_Vendors_Model_Order $order
     * @author Ultimate Module Creator
     */
    protected function _setOrder(Xcentia_Vendors_Model_Order $order) {
        $this->_order = $order;
    }

    /**
     * get current order
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Order
     * @author Ultimate Module Creator
     */
    protected function _getOrder() {
        if (is_null($this->_order)) {
            $orderId = $this->getRequest()->getParam('id');
            $order = Mage::getModel('xcentia_vendors/order');
            $order->load($orderId);
            if (!($order->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_order = $order;
        }
        return $this->_order;
    }
}
