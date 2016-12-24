<?php
/**
 * Xcentia_Mobile extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Mobile
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Device admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Mobile
 * @author      Ultimate Module Creator
 */
class Xcentia_Mobile_Adminhtml_Mobile_DeviceController extends Xcentia_Mobile_Controller_Adminhtml_Mobile
{
    /**
     * init the device
     *
     * @access protected
     * @return Xcentia_Mobile_Model_Device
     */
    protected function _initDevice()
    {
        $deviceId  = (int) $this->getRequest()->getParam('id');
        $device    = Mage::getModel('xcentia_mobile/device');
        if ($deviceId) {
            $device->load($deviceId);
        }
        Mage::register('current_device', $device);
        return $device;
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
        $this->_title(Mage::helper('xcentia_mobile')->__('App Devices'))
             ->_title(Mage::helper('xcentia_mobile')->__('Devices'));
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
     * edit device - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $deviceId    = $this->getRequest()->getParam('id');
        $device      = $this->_initDevice();
        if ($deviceId && !$device->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_mobile')->__('This device no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getDeviceData(true);
        if (!empty($data)) {
            $device->setData($data);
        }
        Mage::register('device_data', $device);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_mobile')->__('App Devices'))
             ->_title(Mage::helper('xcentia_mobile')->__('Devices'));
        if ($device->getId()) {
            $this->_title($device->getGcmId());
        } else {
            $this->_title(Mage::helper('xcentia_mobile')->__('Add device'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new device action
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
     * save device - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('device')) {
            try {
                $device = $this->_initDevice();
                $device->addData($data);
                $device->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_mobile')->__('Device was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $device->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setDeviceData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_mobile')->__('There was a problem saving the device.')
                );
                Mage::getSingleton('adminhtml/session')->setDeviceData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_mobile')->__('Unable to find device to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete device - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $device = Mage::getModel('xcentia_mobile/device');
                $device->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_mobile')->__('Device was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_mobile')->__('There was an error deleting device.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_mobile')->__('Could not find device to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete device - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $deviceIds = $this->getRequest()->getParam('device');
        if (!is_array($deviceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_mobile')->__('Please select devices to delete.')
            );
        } else {
            try {
                foreach ($deviceIds as $deviceId) {
                    $device = Mage::getModel('xcentia_mobile/device');
                    $device->setId($deviceId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_mobile')->__('Total of %d devices were successfully deleted.', count($deviceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_mobile')->__('There was an error deleting devices.')
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
        $deviceIds = $this->getRequest()->getParam('device');
        if (!is_array($deviceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_mobile')->__('Please select devices.')
            );
        } else {
            try {
                foreach ($deviceIds as $deviceId) {
                $device = Mage::getSingleton('xcentia_mobile/device')->load($deviceId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d devices were successfully updated.', count($deviceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_mobile')->__('There was an error updating devices.')
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
        $fileName   = 'device.csv';
        $content    = $this->getLayout()->createBlock('xcentia_mobile/adminhtml_device_grid')
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
        $fileName   = 'device.xls';
        $content    = $this->getLayout()->createBlock('xcentia_mobile/adminhtml_device_grid')
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
        $fileName   = 'device.xml';
        $content    = $this->getLayout()->createBlock('xcentia_mobile/adminhtml_device_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_mobile/device');
    }
}
