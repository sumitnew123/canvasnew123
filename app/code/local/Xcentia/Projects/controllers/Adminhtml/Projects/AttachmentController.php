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
 * Attachment admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Adminhtml_Projects_AttachmentController extends Xcentia_Projects_Controller_Adminhtml_Projects
{
    /**
     * init the attachment
     *
     * @access protected
     * @return Xcentia_Projects_Model_Attachment
     */
    protected function _initAttachment()
    {
        $attachmentId  = (int) $this->getRequest()->getParam('id');
        $attachment    = Mage::getModel('xcentia_projects/attachment');
        if ($attachmentId) {
            $attachment->load($attachmentId);
        }
        Mage::register('current_attachment', $attachment);
        return $attachment;
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
             ->_title(Mage::helper('xcentia_projects')->__('Attachments'));
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
     * edit attachment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $attachmentId    = $this->getRequest()->getParam('id');
        $attachment      = $this->_initAttachment();
        if ($attachmentId && !$attachment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_projects')->__('This attachment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getAttachmentData(true);
        if (!empty($data)) {
            $attachment->setData($data);
        }
        Mage::register('attachment_data', $attachment);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_projects')->__('Projects'))
             ->_title(Mage::helper('xcentia_projects')->__('Attachments'));
        if ($attachment->getId()) {
            $this->_title($attachment->getName());
        } else {
            $this->_title(Mage::helper('xcentia_projects')->__('Add attachment'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new attachment action
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
     * save attachment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('attachment')) {
            try {
                $attachment = $this->_initAttachment();
                $attachment->addData($data);
                $attachment->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Attachment was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $attachment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAttachmentData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was a problem saving the attachment.')
                );
                Mage::getSingleton('adminhtml/session')->setAttachmentData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_projects')->__('Unable to find attachment to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete attachment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $attachment = Mage::getModel('xcentia_projects/attachment');
                $attachment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Attachment was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error deleting attachment.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_projects')->__('Could not find attachment to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete attachment - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $attachmentIds = $this->getRequest()->getParam('attachment');
        if (!is_array($attachmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select attachments to delete.')
            );
        } else {
            try {
                foreach ($attachmentIds as $attachmentId) {
                    $attachment = Mage::getModel('xcentia_projects/attachment');
                    $attachment->setId($attachmentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Total of %d attachments were successfully deleted.', count($attachmentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error deleting attachments.')
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
        $attachmentIds = $this->getRequest()->getParam('attachment');
        if (!is_array($attachmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select attachments.')
            );
        } else {
            try {
                foreach ($attachmentIds as $attachmentId) {
                $attachment = Mage::getSingleton('xcentia_projects/attachment')->load($attachmentId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d attachments were successfully updated.', count($attachmentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating attachments.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass message change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massMessageIdAction()
    {
        $attachmentIds = $this->getRequest()->getParam('attachment');
        if (!is_array($attachmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select attachments.')
            );
        } else {
            try {
                foreach ($attachmentIds as $attachmentId) {
                $attachment = Mage::getSingleton('xcentia_projects/attachment')->load($attachmentId)
                    ->setMessageId($this->getRequest()->getParam('flag_message_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d attachments were successfully updated.', count($attachmentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating attachments.')
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
        $fileName   = 'attachment.csv';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_attachment_grid')
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
        $fileName   = 'attachment.xls';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_attachment_grid')
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
        $fileName   = 'attachment.xml';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_attachment_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_projects/attachment');
    }
}
