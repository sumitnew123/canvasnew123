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
 * Service admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Adminhtml_Vendors_ServiceController extends Xcentia_Vendors_Controller_Adminhtml_Vendors
{
    /**
     * init the service
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Service
     */
    protected function _initService()
    {
        $serviceId  = (int) $this->getRequest()->getParam('id');
        $service    = Mage::getModel('xcentia_vendors/service');
        if ($serviceId) {
            $service->load($serviceId);
        }
        Mage::register('current_service', $service);
        return $service;
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
             ->_title(Mage::helper('xcentia_vendors')->__('Services'));
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
     * edit service - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $serviceId    = $this->getRequest()->getParam('id');
        $service      = $this->_initService();
        if ($serviceId && !$service->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_vendors')->__('This service no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getServiceData(true);
        if (!empty($data)) {
            $service->setData($data);
        }
        Mage::register('service_data', $service);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Services'));
        if ($service->getId()) {
            $this->_title($service->getTitle());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add service'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new service action
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
     * save service - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('service')) {
            try {
                $service = $this->_initService();
                $service->addData($data);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $service->setCategoriesData($categories);
                }
                $service->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Service was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $service->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setServiceData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the service.')
                );
                Mage::getSingleton('adminhtml/session')->setServiceData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find service to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete service - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $service = Mage::getModel('xcentia_vendors/service');
                $service->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Service was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting service.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find service to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete service - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $serviceIds = $this->getRequest()->getParam('service');
        if (!is_array($serviceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select services to delete.')
            );
        } else {
            try {
                foreach ($serviceIds as $serviceId) {
                    $service = Mage::getModel('xcentia_vendors/service');
                    $service->setId($serviceId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Total of %d services were successfully deleted.', count($serviceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting services.')
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
        $serviceIds = $this->getRequest()->getParam('service');
        if (!is_array($serviceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select services.')
            );
        } else {
            try {
                foreach ($serviceIds as $serviceId) {
                $service = Mage::getSingleton('xcentia_vendors/service')->load($serviceId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d services were successfully updated.', count($serviceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating services.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesAction()
    {
        $this->_initService();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesJsonAction()
    {
        $this->_initService();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('xcentia_vendors/adminhtml_service_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
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
        $fileName   = 'service.csv';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_service_grid')
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
        $fileName   = 'service.xls';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_service_grid')
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
        $fileName   = 'service.xml';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_service_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_vendors/service');
    }
}
