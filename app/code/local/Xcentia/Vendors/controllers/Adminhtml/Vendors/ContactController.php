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
 * Contact admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_Adminhtml_Vendors_ContactController extends Xcentia_Vendors_Controller_Adminhtml_Vendors
{
    /**
     * init the contact
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Contact
     */
    protected function _initContact()
    {
        $contactId  = (int) $this->getRequest()->getParam('id');
        $contact    = Mage::getModel('xcentia_vendors/contact');
        if ($contactId) {
            $contact->load($contactId);
        }
        Mage::register('current_contact', $contact);
        return $contact;
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
        $this->_title(Mage::helper('xcentia_vendors')->__('Contacts'))
             ->_title(Mage::helper('xcentia_vendors')->__('Contacts'));
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
     * edit contact - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $contactId    = $this->getRequest()->getParam('id');
        $contact      = $this->_initContact();
        if ($contactId && !$contact->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_vendors')->__('This contact no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getContactData(true);
        if (!empty($data)) {
            $contact->setData($data);
        }
        Mage::register('contact_data', $contact);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_vendors')->__('Contacts'))
             ->_title(Mage::helper('xcentia_vendors')->__('Contacts'));
        if ($contact->getId()) {
            $this->_title($contact->getName());
        } else {
            $this->_title(Mage::helper('xcentia_vendors')->__('Add contact'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new contact action
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
     * save contact - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('contact')) {
            try {
                $contact = $this->_initContact();
                $contact->addData($data);
                $contact->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Contact was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $contact->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setContactData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the contact.')
                );
                Mage::getSingleton('adminhtml/session')->setContactData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find contact to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete contact - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $contact = Mage::getModel('xcentia_vendors/contact');
                $contact->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Contact was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting contact.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find contact to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete contact - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $contactIds = $this->getRequest()->getParam('contact');
        if (!is_array($contactIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select contacts to delete.')
            );
        } else {
            try {
                foreach ($contactIds as $contactId) {
                    $contact = Mage::getModel('xcentia_vendors/contact');
                    $contact->setId($contactId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Total of %d contacts were successfully deleted.', count($contactIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting contacts.')
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
        $contactIds = $this->getRequest()->getParam('contact');
        if (!is_array($contactIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_vendors')->__('Please select contacts.')
            );
        } else {
            try {
                foreach ($contactIds as $contactId) {
                $contact = Mage::getSingleton('xcentia_vendors/contact')->load($contactId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d contacts were successfully updated.', count($contactIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error updating contacts.')
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
        $fileName   = 'contact.csv';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_contact_grid')
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
        $fileName   = 'contact.xls';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_contact_grid')
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
        $fileName   = 'contact.xml';
        $content    = $this->getLayout()->createBlock('xcentia_vendors/adminhtml_contact_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_vendors/contact');
    }
}
