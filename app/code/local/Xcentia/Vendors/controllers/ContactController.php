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
 * Contact front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_ContactController extends Mage_Core_Controller_Front_Action
{

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
		$contactId    = $this->getRequest()->getParam('id');
        $contact      = $this->_initContact();
        Mage::register('current_contact', $contact);
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('xcentia_vendors/contact')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('xcentia_vendors')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'contacts',
                    array(
                        'label' => Mage::helper('xcentia_vendors')->__('Contacts'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_vendors/contact')->getContactsUrl());
        }
        $this->renderLayout();
    }

    /**
     * init Contact
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Contact
     * @author Ultimate Module Creator
     */
    protected function _initContact()
    {
        $contactId   = $this->getRequest()->getParam('id', 0);
        $contact     = Mage::getModel('xcentia_vendors/contact')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($contactId);
        return $contact;
    }
	
	public function editAction()
    {
        $contactId    = $this->getRequest()->getParam('id');
        $contact      = $this->_initContact();
        Mage::register('current_contact', $contact);
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
	 public function newAction()
    {
        $this->_forward('edit');
    }
	
	 public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
				
                $contact = $this->_initContact();
                $contact->addData($data);
				$contact->setStatus(1);
                $contact->save();
                Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Contact was successfully saved')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the contact.')
                );
                Mage::getSingleton('customer/session')->setContactData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('customer/session')->addError(
            Mage::helper('xcentia_vendors')->__('Unable to find contact to save.')
        );
        $this->_redirect('*/*/');
    }

	 public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $contact = Mage::getModel('xcentia_vendors/contact');
                $contact->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Contact was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was an error deleting contact.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('customer/session')->addError(
            Mage::helper('xcentia_vendors')->__('Could not find contact to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * view contact action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $contact = $this->_initContact();
        if (!$contact) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_contact', $contact);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('vendors-contact vendors-contact' . $contact->getId());
        }
        if (Mage::helper('xcentia_vendors/contact')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('xcentia_vendors')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'contacts',
                    array(
                        'label' => Mage::helper('xcentia_vendors')->__('Contacts'),
                        'link'  => Mage::helper('xcentia_vendors/contact')->getContactsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'contact',
                    array(
                        'label' => $contact->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $contact->getContactUrl());
        }
        $this->renderLayout();
    }
}
