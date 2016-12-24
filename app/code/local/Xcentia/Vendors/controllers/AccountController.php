<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Xcentia_Vendors_AccountController extends Mage_Core_Controller_Front_Action
{
    const CUSTOMER_ID_SESSION_NAME = "customerId";
    const TOKEN_SESSION_NAME = "token";

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'createpost');

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'changeforgotten',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from customer session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

    /**
     * Default customer account page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }
    
 	public function profileAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Profile'));
        $this->renderLayout();
    }

 
    /**
     * Get Customer Model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        $customer = $this->_getFromRegistry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }


    /**
     * Forgot customer account information page
     */
	public function editAction()
    {	
    	$vendorId = (int)Mage::helper('xcentia_vendors')->getDealer()->getId();
    	$vendor	= Mage::getModel('xcentia_vendors/vendor')
            		->setStoreId(Mage::app()->getStore()->getId())
            		->load($vendorId);
        Mage::register('current_vendor', $vendor);
    	$headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle('Edit Vendor');
        }
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
	
	public function editcoverAction()
    {	
    	$vendorId = (int)Mage::helper('xcentia_vendors')->getDealer()->getId();
    	$vendor	= Mage::getModel('xcentia_vendors/vendor')
            		->setStoreId(Mage::app()->getStore()->getId())
            		->load($vendorId);
        Mage::register('current_vendor', $vendor);
    	$headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle('Edit Vendor Cover & Logo');
        }
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }
		if ($data = $this->getRequest()->getPost()) {
            try {
                $vendorId = (int)Mage::helper('xcentia_vendors')->getDealer()->getId();
    			$vendor	= Mage::getModel('xcentia_vendors/vendor')
            				->setStoreId(Mage::app()->getStore()->getId())
            				->load($vendorId);
            	if($vendor->getEmail() != $data['email']) {
            		$customer = Mage::helper('customer')->getCustomer();
            		$customer->setEmail($data['email'])->save();
            	}
            	if($vendor->getPhone() != $data['phone']) {
            		$customer = Mage::helper('customer')->getCustomer();
            		$customer->setPhone($data['phone'])->save();
            	}
                $vendor->addData($data);
                $vendor->save();
                Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Vendor was successfully saved')
                );
                Mage::getSingleton('customer/session')->setFormData(false);
                $this->_redirect('*/*/profile');
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the vendor.')
                );
                $this->_redirect('*/*/edit');
                return;
            }
        }
        $this->_redirect('*/*/profile');
    }
    
	public function editcoverPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/editcover');
        }
		if ($data = $this->getRequest()->getPost()) {
            try {
                $vendorId = (int)Mage::helper('xcentia_vendors')->getDealer()->getId();
    			$vendor	= Mage::getModel('xcentia_vendors/vendor')
            				->setStoreId(Mage::app()->getStore()->getId())
            				->load($vendorId);
                $logoName = $this->_uploadAndGetName(
                    'logo',
                    Mage::helper('xcentia_vendors/vendor_image')->getImageBaseDir(),
                    $data
                );
                if($logoName != '') $vendor->setData('logo', $logoName);
                $coverName = $this->_uploadAndGetName(
                    'cover',
                    Mage::helper('xcentia_vendors/vendor_image')->getImageBaseDir(),
                    $data
                );
                if($coverName != '') $vendor->setData('cover', $coverName);
                
                $vendor->save();
                Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Vendor was successfully saved')
                );
                Mage::getSingleton('customer/session')->setFormData(false);
                $this->_redirect('*/*/profile');
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the vendor.')
                );
                $this->_redirect('*/*/editcover');
                return;
            }
        }
        $this->_redirect('*/*/profile');
    }
    
	protected function _uploadAndGetName($input, $destinationFolder, $data)
    {
        try {
            if (isset($data[$input]['delete'])) {
                return '';
            } else {
                $uploader = new Varien_File_Uploader($input);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }
        } catch (Exception $e) {
            if ($e->getCode() != Varien_File_Uploader::TMP_NAME_EMPTY) {
                throw $e;
            } else {
                if (isset($data[$input]['value'])) {
                    return $data[$input]['value'];
                }
            }
        }
        return '';
    }
}
