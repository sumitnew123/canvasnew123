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
 * Coverage admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Adminhtml_Vendors_CoverageController extends Xcentia_Vendors_Controller_Adminhtml_Vendors
{
    /**
     * init the coverage
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Coverage
     */
    protected function _initCoverage()
    {
        $coverageId  = (int) $this->getRequest()->getParam('id');
        $coverage    = Mage::getModel('xcentia_vendors/coverage');
        if ($coverageId) {
            $coverage->load($coverageId);
        }
        Mage::register('current_coverage', $coverage);
        return $coverage;
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
             ->_title(Mage::helper('xcentia_vendors')->__('Coverages'));
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
     * edit coverage - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $coverageId    = $this->getRequest()->getParam('id');
        $coverage      = $this->_initCoverage();
        if ($coverageId && !$coverage->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_vendors')->__('This coverage no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCoverageData(true);
        if (!empty($data)) {
            $coverage->setData($data);
        }
        Mage::register('coverage_data', $coverage);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Vendors'))
             ->_title(Mage::helper('xcentia_vendors')->__('Coverages'));
        if ($coverage->getId()) {
            $this->_title($coverage->getCity());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add coverage'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new coverage action
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
     * save coverage - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('coverage')) {
            try {
                $coverage = $this->_initCoverage();
                $coverage->addData($data);
                $coverage->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Coverage was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $coverage->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCoverageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the coverage.')
                );
                Mage::getSingleton('adminhtml/session')->setCoverageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find coverage to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete coverage - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $coverage = Mage::getModel('xcentia_vendors/coverage');
                $coverage->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Coverage was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting coverage.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find coverage to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete coverage - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $coverageIds = $this->getRequest()->getParam('coverage');
        if (!is_array($coverageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select coverages to delete.')
            );
        } else {
            try {
                foreach ($coverageIds as $coverageId) {
                    $coverage = Mage::getModel('xcentia_vendors/coverage');
                    $coverage->setId($coverageId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Total of %d coverages were successfully deleted.', count($coverageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting coverages.')
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
        $coverageIds = $this->getRequest()->getParam('coverage');
        if (!is_array($coverageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select coverages.')
            );
        } else {
            try {
                foreach ($coverageIds as $coverageId) {
                $coverage = Mage::getSingleton('xcentia_vendors/coverage')->load($coverageId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d coverages were successfully updated.', count($coverageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating coverages.')
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
        $coverageIds = $this->getRequest()->getParam('coverage');
        if (!is_array($coverageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select coverages.')
            );
        } else {
            try {
                foreach ($coverageIds as $coverageId) {
                $coverage = Mage::getSingleton('xcentia_vendors/coverage')->load($coverageId)
                    ->setCountry($this->getRequest()->getParam('flag_country'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d coverages were successfully updated.', count($coverageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating coverages.')
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
        $coverageIds = $this->getRequest()->getParam('coverage');
        if (!is_array($coverageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select coverages.')
            );
        } else {
            try {
                foreach ($coverageIds as $coverageId) {
                $coverage = Mage::getSingleton('xcentia_vendors/coverage')->load($coverageId)
                    ->setVendorId($this->getRequest()->getParam('flag_vendor_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d coverages were successfully updated.', count($coverageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating coverages.')
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
        $fileName   = 'coverage.csv';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_coverage_grid')
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
        $fileName   = 'coverage.xls';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_coverage_grid')
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
        $fileName   = 'coverage.xml';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_coverage_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_vendors/coverage');
    }
}
