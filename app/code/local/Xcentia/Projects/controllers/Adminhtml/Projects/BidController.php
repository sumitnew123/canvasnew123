<?php
/**
 * Xcentia_Projects extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Projects
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Bid admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Adminhtml_Projects_BidController extends Xcentia_Projects_Controller_Adminhtml_Projects
{
    /**
     * init the bid
     *
     * @access protected
     * @return Xcentia_Projects_Model_Bid
     */
    protected function _initBid()
    {
        $bidId  = (int) $this->getRequest()->getParam('id');
        $bid    = Mage::getModel('xcentia_projects/bid');
        if ($bidId) {
            $bid->load($bidId);
        }
        Mage::register('current_bid', $bid);
        return $bid;
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
        $this->_title(Mage::helper('xcentia_projects')->__('Projects'))
             ->_title(Mage::helper('xcentia_projects')->__('Bids'));
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
     * edit bid - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $bidId    = $this->getRequest()->getParam('id');
        $bid      = $this->_initBid();
        if ($bidId && !$bid->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_projects')->__('This bid no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getBidData(true);
        if (!empty($data)) {
            $bid->setData($data);
        }
        Mage::register('bid_data', $bid);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_projects')->__('Projects'))
             ->_title(Mage::helper('xcentia_projects')->__('Bids'));
        if ($bid->getId()) {
            $this->_title($bid->getAmount());
        } else {
            $this->_title(Mage::helper('xcentia_projects')->__('Add bid'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new bid action
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
     * save bid - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('bid')) {
            try {
                $data = $this->_filterDates($data, array('date'));
                $bid = $this->_initBid();
                $bid->addData($data);
                $bid->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Bid was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $bid->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setBidData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was a problem saving the bid.')
                );
                Mage::getSingleton('adminhtml/session')->setBidData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_projects')->__('Unable to find bid to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete bid - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $bid = Mage::getModel('xcentia_projects/bid');
                $bid->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Bid was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error deleting bid.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_projects')->__('Could not find bid to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete bid - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $bidIds = $this->getRequest()->getParam('bid');
        if (!is_array($bidIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select bids to delete.')
            );
        } else {
            try {
                foreach ($bidIds as $bidId) {
                    $bid = Mage::getModel('xcentia_projects/bid');
                    $bid->setId($bidId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Total of %d bids were successfully deleted.', count($bidIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error deleting bids.')
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
        $bidIds = $this->getRequest()->getParam('bid');
        if (!is_array($bidIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select bids.')
            );
        } else {
            try {
                foreach ($bidIds as $bidId) {
                $bid = Mage::getSingleton('xcentia_projects/bid')->load($bidId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d bids were successfully updated.', count($bidIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating bids.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Is Selected change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massSelectedAction()
    {
        $bidIds = $this->getRequest()->getParam('bid');
        if (!is_array($bidIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select bids.')
            );
        } else {
            try {
                foreach ($bidIds as $bidId) {
                $bid = Mage::getSingleton('xcentia_projects/bid')->load($bidId)
                    ->setSelected($this->getRequest()->getParam('flag_selected'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d bids were successfully updated.', count($bidIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating bids.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass project change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massProjectIdAction()
    {
        $bidIds = $this->getRequest()->getParam('bid');
        if (!is_array($bidIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select bids.')
            );
        } else {
            try {
                foreach ($bidIds as $bidId) {
                $bid = Mage::getSingleton('xcentia_projects/bid')->load($bidId)
                    ->setProjectId($this->getRequest()->getParam('flag_project_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d bids were successfully updated.', count($bidIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating bids.')
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
        $fileName   = 'bid.csv';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_bid_grid')
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
        $fileName   = 'bid.xls';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_bid_grid')
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
        $fileName   = 'bid.xml';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_bid_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_projects/bid');
    }
}
