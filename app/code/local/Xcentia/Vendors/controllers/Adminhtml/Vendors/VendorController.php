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
 * Vendor admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Adminhtml_Vendors_VendorController extends Xcentia_Vendors_Controller_Adminhtml_Vendors
{
    /**
     * init the vendor
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Vendor
     */
    protected function _initVendor()
    {
        $vendorId  = (int) $this->getRequest()->getParam('id');
        $vendor    = Mage::getModel('xcentia_vendors/vendor');
        if ($vendorId) {
            $vendor->load($vendorId);
        }
        Mage::register('current_vendor', $vendor);
        return $vendor;
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
             ->_title(Mage::helper('xcentia_vendors')->__('Vendors'));
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
     * edit vendor - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $vendorId    = $this->getRequest()->getParam('id');
        $vendor      = $this->_initVendor();
        if ($vendorId && !$vendor->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_vendors')->__('This vendor no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getVendorData(true);
        if (!empty($data)) {
            $vendor->setData($data);
        }
        Mage::register('vendor_data', $vendor);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Vendors'));
        if ($vendor->getId()) {
            $this->_title($vendor->getTitle());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add vendor'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new vendor action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        //$this->_forward('edit');
        $vendor      = $this->_initVendor();
        $data = Mage::getSingleton('adminhtml/session')->getVendorData(true);
        if (!empty($data)) {
            $vendor->setData($data);
        }
        Mage::register('vendor_data', $vendor);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Vendors'));
        if ($vendor->getId()) {
            $this->_title($vendor->getTitle());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add vendor'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * save vendor - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('vendor')) {
            try {
                $vendor = $this->_initVendor();
                $vendor->addData($data);
                $logoName = $this->_uploadAndGetName(
                    'logo',
                    Mage::helper('xcentia_vendors/vendor_image')->getImageBaseDir(),
                    $data
                );
                $vendor->setData('logo', $logoName);
                $coverName = $this->_uploadAndGetName(
                    'cover',
                    Mage::helper('xcentia_vendors/vendor_image')->getImageBaseDir(),
                    $data
                );
                $vendor->setData('cover', $coverName);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $vendor->setCategoriesData($categories);
                }
                $vendor->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Vendor was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $vendor->getId()));
                    return;
                }

                if ($this->getRequest()->getParam('approve')  || $data['firstname'] != '') {
                   $customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($vendor->getEmail());
                   if($customer->getId() > 0) {
                        $customer->setGroupId(2);
                        $isNew = false;
                    } else {
                        $customer->setWebsiteId(1)
                            ->setStore(Mage::app()->getStore(1))
                            ->setGroupId(2)
                            ->setFirstname($vendor->getFirstname())
                            ->setLastname($vendor->getLastname())
                            ->setEmail($vendor->getEmail())
                            //->setPassword(Mage::helper('core')->getRandomString(8));
                            ->setPassword('bulbandkey');
                        $isNew = true;
                    }
                    $customer->save();
                    if($isNew){  
                        $customer->sendNewAccountEmail('registered','', 1);
                    }
                    $vendor->setCustomerId($customer->getId())->setStatus(1)->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('xcentia_vendors')->__('Vendor was successfully Created and Notification Sent.')
                    );
                }

                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                if (isset($data['logo']['value'])) {
                    $data['logo'] = $data['logo']['value'];
                }
                if (isset($data['cover']['value'])) {
                    $data['cover'] = $data['cover']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setVendorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                if (isset($data['logo']['value'])) {
                    $data['logo'] = $data['logo']['value'];
                }
                if (isset($data['cover']['value'])) {
                    $data['cover'] = $data['cover']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the vendor.')
                );
                Mage::getSingleton('adminhtml/session')->setVendorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find vendor to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete vendor - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $vendor = Mage::getModel('xcentia_vendors/vendor');
                $vendor->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Vendor was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting vendor.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find vendor to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete vendor - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $vendorIds = $this->getRequest()->getParam('vendor');
        if (!is_array($vendorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select vendors to delete.')
            );
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                    $vendor = Mage::getModel('xcentia_vendors/vendor');
                    $vendor->setId($vendorId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Total of %d vendors were successfully deleted.', count($vendorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting vendors.')
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
        $vendorIds = $this->getRequest()->getParam('vendor');
        if (!is_array($vendorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select vendors.')
            );
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                $vendor = Mage::getSingleton('xcentia_vendors/vendor')->load($vendorId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d vendors were successfully updated.', count($vendorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating vendors.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Country change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massCountryAction()
    {
        $vendorIds = $this->getRequest()->getParam('vendor');
        if (!is_array($vendorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select vendors.')
            );
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                $vendor = Mage::getSingleton('xcentia_vendors/vendor')->load($vendorId)
                    ->setCountry($this->getRequest()->getParam('flag_country'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d vendors were successfully updated.', count($vendorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating vendors.')
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
        $this->_initVendor();
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
        $this->_initVendor();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('xcentia_vendors/adminhtml_vendor_edit_tab_categories')
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
        $fileName   = 'vendor.csv';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_vendor_grid')
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
        $fileName   = 'vendor.xls';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_vendor_grid')
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
        $fileName   = 'vendor.xml';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_vendor_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_vendors/vendor');
    }
}
