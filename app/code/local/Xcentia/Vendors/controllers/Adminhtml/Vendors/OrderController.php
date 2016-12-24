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
 * Order admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Adminhtml_Vendors_OrderController extends Xcentia_Vendors_Controller_Adminhtml_Vendors
{
    /**
     * init the order
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Order
     */
    protected function _initOrder()
    {
        $orderId  = (int) $this->getRequest()->getParam('id');
        $order    = Mage::getModel('xcentia_vendors/order');
        if ($orderId) {
            $order->load($orderId);
        }
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Orders'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit order - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $orderId    = $this->getRequest()->getParam('id');
        $order      = $this->_initOrder();
        if ($orderId && !$order->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_vendors')->__('This order no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getOrderData(true);
        if (!empty($data)) {
            $order->setData($data);
        }
        Mage::register('order_data', $order);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Orders'));
        if ($order->getId()) {
            $this->_title($order->getOrderId());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add order'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new order action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save order - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('order')) {
            try {
                $order = $this->_initOrder();
                $order->addData($data);
                $order->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Order was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $order->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setOrderData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the order.')
                );
                Mage::getSingleton('adminhtml/session')->setOrderData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find order to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete order - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $order = Mage::getModel('xcentia_vendors/order');
                $order->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Order was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting order.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find order to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete order - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $orderIds = $this->getRequest()->getParam('order');
        if (!is_array($orderIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select orders to delete.')
            );
        } else {
            try {
                foreach ($orderIds as $orderId) {
                    $order = Mage::getModel('xcentia_vendors/order');
                    $order->setId($orderId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Total of %d orders were successfully deleted.', count($orderIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting orders.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $orderIds = $this->getRequest()->getParam('order');
        if (!is_array($orderIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select orders.')
            );
        } else {
            try {
                foreach ($orderIds as $orderId) {
                $order = Mage::getSingleton('xcentia_vendors/order')->load($orderId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d orders were successfully updated.', count($orderIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating orders.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass vendor change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massVendorIdAction()
    {
        $orderIds = $this->getRequest()->getParam('order');
        if (!is_array($orderIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select orders.')
            );
        } else {
            try {
                foreach ($orderIds as $orderId) {
                $order = Mage::getSingleton('xcentia_vendors/order')->load($orderId)
                    ->setVendorId($this->getRequest()->getParam('flag_vendor_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d orders were successfully updated.', count($orderIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating orders.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'order.csv';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_order_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'order.xls';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_order_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'order.xml';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_order_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_vendors/order');
    }
}
