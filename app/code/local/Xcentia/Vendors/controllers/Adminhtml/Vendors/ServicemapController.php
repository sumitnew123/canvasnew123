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
 * Servicemap admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Adminhtml_Vendors_ServicemapController extends Xcentia_Vendors_Controller_Adminhtml_Vendors
{
    /**
     * init the servicemap
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Servicemap
     */
    protected function _initServicemap()
    {
        $servicemapId  = (int) $this->getRequest()->getParam('id');
        $servicemap    = Mage::getModel('xcentia_vendors/servicemap');
        if ($servicemapId) {
            $servicemap->load($servicemapId);
        }
        Mage::register('current_servicemap', $servicemap);
        return $servicemap;
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
             ->_title(Mage::helper('xcentia_vendors')->__('Servicemaps'));
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
     * edit servicemap - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $servicemapId    = $this->getRequest()->getParam('id');
        $servicemap      = $this->_initServicemap();
        if ($servicemapId && !$servicemap->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_vendors')->__('This servicemap no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getServicemapData(true);
        if (!empty($data)) {
            $servicemap->setData($data);
        }
        Mage::register('servicemap_data', $servicemap);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Servicemaps'));
        if ($servicemap->getId()) {
            $this->_title($servicemap->getVendorId());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add servicemap'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new servicemap action
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
     * save servicemap - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('servicemap')) {
            try {
                $servicemap = $this->_initServicemap();
                $servicemap->addData($data);
                $servicemap->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Servicemap was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $servicemap->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setServicemapData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the servicemap.')
                );
                Mage::getSingleton('adminhtml/session')->setServicemapData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find servicemap to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete servicemap - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $servicemap = Mage::getModel('xcentia_vendors/servicemap');
                $servicemap->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Servicemap was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting servicemap.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find servicemap to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete servicemap - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $servicemapIds = $this->getRequest()->getParam('servicemap');
        if (!is_array($servicemapIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select servicemaps to delete.')
            );
        } else {
            try {
                foreach ($servicemapIds as $servicemapId) {
                    $servicemap = Mage::getModel('xcentia_vendors/servicemap');
                    $servicemap->setId($servicemapId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Total of %d servicemaps were successfully deleted.', count($servicemapIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting servicemaps.')
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
        $servicemapIds = $this->getRequest()->getParam('servicemap');
        if (!is_array($servicemapIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select servicemaps.')
            );
        } else {
            try {
                foreach ($servicemapIds as $servicemapId) {
                $servicemap = Mage::getSingleton('xcentia_vendors/servicemap')->load($servicemapId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d servicemaps were successfully updated.', count($servicemapIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating servicemaps.')
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
        $fileName   = 'servicemap.csv';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_servicemap_grid')
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
        $fileName   = 'servicemap.xls';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_servicemap_grid')
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
        $fileName   = 'servicemap.xml';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_servicemap_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_vendors/servicemap');
    }
}
