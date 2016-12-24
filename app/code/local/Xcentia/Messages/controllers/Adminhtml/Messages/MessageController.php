<?php
/**
 * Xcentia_Messages extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xcentia
 * @package        Xcentia_Messages
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Message admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_Adminhtml_Messages_MessageController extends Xcentia_Messages_Controller_Adminhtml_Messages
{
    /**
     * init the message
     *
     * @access protected
     * @return Xcentia_Messages_Model_Message
     */
    protected function _initMessage()
    {
        $messageId  = (int) $this->getRequest()->getParam('id');
        $message    = Mage::getModel('xcentia_messages/message');
        if ($messageId) {
            $message->load($messageId);
        }
        Mage::register('current_message', $message);
        return $message;
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
        $this->_title(Mage::helper('xcentia_messages')->__('Messages'))
             ->_title(Mage::helper('xcentia_messages')->__('Messages'));
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
     * edit message - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $messageId    = $this->getRequest()->getParam('id');
        $message      = $this->_initMessage();
        if ($messageId && !$message->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_messages')->__('This message no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getMessageData(true);
        if (!empty($data)) {
            $message->setData($data);
        }
        Mage::register('message_data', $message);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_messages')->__('Messages'))
             ->_title(Mage::helper('xcentia_messages')->__('Messages'));
        if ($message->getId()) {
            $this->_title($message->getSubject());
        } else {
            $this->_title(Mage::helper('xcentia_messages')->__('Add message'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new message action
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
     * save message - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('message')) {
            try {
                $message = $this->_initMessage();
                $message->addData($data);
                $message->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_messages')->__('Message was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $message->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setMessageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was a problem saving the message.')
                );
                Mage::getSingleton('adminhtml/session')->setMessageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_messages')->__('Unable to find message to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete message - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $message = Mage::getModel('xcentia_messages/message');
                $message->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_messages')->__('Message was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was an error deleting message.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_messages')->__('Could not find message to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete message - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $messageIds = $this->getRequest()->getParam('message');
        if (!is_array($messageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_messages')->__('Please select messages to delete.')
            );
        } else {
            try {
                foreach ($messageIds as $messageId) {
                    $message = Mage::getModel('xcentia_messages/message');
                    $message->setId($messageId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_messages')->__('Total of %d messages were successfully deleted.', count($messageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was an error deleting messages.')
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
        $messageIds = $this->getRequest()->getParam('message');
        if (!is_array($messageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_messages')->__('Please select messages.')
            );
        } else {
            try {
                foreach ($messageIds as $messageId) {
                $message = Mage::getSingleton('xcentia_messages/message')->load($messageId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d messages were successfully updated.', count($messageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was an error updating messages.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Owner change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massOwnerAction()
    {
        $messageIds = $this->getRequest()->getParam('message');
        if (!is_array($messageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_messages')->__('Please select messages.')
            );
        } else {
            try {
                foreach ($messageIds as $messageId) {
                $message = Mage::getSingleton('xcentia_messages/message')->load($messageId)
                    ->setOwner($this->getRequest()->getParam('flag_owner'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d messages were successfully updated.', count($messageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was an error updating messages.')
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
        $fileName   = 'message.csv';
        $content    = $this->getLayout()->createBlock('xcentia_messages/adminhtml_message_grid')
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
        $fileName   = 'message.xls';
        $content    = $this->getLayout()->createBlock('xcentia_messages/adminhtml_message_grid')
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
        $fileName   = 'message.xml';
        $content    = $this->getLayout()->createBlock('xcentia_messages/adminhtml_message_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_messages/message');
    }
}
