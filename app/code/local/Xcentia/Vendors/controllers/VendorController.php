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
 * Vendor front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
class Xcentia_Vendors_VendorController extends Mage_Core_Controller_Front_Action
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
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('xcentia_vendors/vendor')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('xcentia_vendors')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'vendors',
                    array(
                        'label' => Mage::helper('xcentia_vendors')->__('Vendors'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_vendors/vendor')->getVendorsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('xcentia_vendors/vendor/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('xcentia_vendors/vendor/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('xcentia_vendors/vendor/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Vendor
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Vendor
     * @author Ultimate Module Creator
     */
    protected function _initVendor()
    {
        $vendorId   = $this->getRequest()->getParam('id', 0);
        $vendor     = Mage::getModel('xcentia_vendors/vendor')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($vendorId);
        if (!$vendor->getId()) {
            return false;
        } elseif (!$vendor->getStatus()) {
            return false;
        }
        return $vendor;
    }

    /**
     * view vendor action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $vendor = $this->_initVendor();
        if (!$vendor) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_vendor', $vendor);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('vendors-vendor vendors-vendor' . $vendor->getId());
        }
        if (Mage::helper('xcentia_vendors/vendor')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('xcentia_vendors')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'vendors',
                    array(
                        'label' => Mage::helper('xcentia_vendors')->__('Vendors'),
                        'link'  => Mage::helper('xcentia_vendors/vendor')->getVendorsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'vendor',
                    array(
                        'label' => $vendor->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $vendor->getVendorUrl());
        }
        if ($headBlock) {
            if ($vendor->getMetaTitle()) {
                $headBlock->setTitle($vendor->getMetaTitle());
            } else {
                $headBlock->setTitle($vendor->getTitle());
            }
            $headBlock->setKeywords($vendor->getMetaKeywords());
            $headBlock->setDescription($vendor->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * Submit new comment action
     * @access public
     * @author Ultimate Module Creator
     */
    public function commentpostAction()
    {
        $data   = $this->getRequest()->getPost();
        $vendor = $this->_initVendor();
        $session    = Mage::getSingleton('core/session');
        if ($vendor) {
            if ($vendor->getAllowComments()) {
                if ((Mage::getSingleton('customer/session')->isLoggedIn() ||
                    Mage::getStoreConfigFlag('xcentia_vendors/vendor/allow_guest_comment'))) {
                    $comment  = Mage::getModel('xcentia_vendors/vendor_comment')->setData($data);
                    $validate = $comment->validate();
                    if ($validate === true) {
                        try {
                            $comment->setVendorId($vendor->getId())
                                ->setStatus(Xcentia_Vendors_Model_Vendor_Comment::STATUS_APPROVED)
                                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                                ->setStores(array(Mage::app()->getStore()->getId()))
                                ->save();
                            $session->addSuccess($this->__('Your comment has been accepted for moderation.'));
                        } catch (Exception $e) {
                            $session->setVendorCommentData($data);
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    } else {
                        $session->setVendorCommentData($data);
                        if (is_array($validate)) {
                            foreach ($validate as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                } else {
                    $session->addError($this->__('Guest comments are not allowed'));
                }
            } else {
                $session->addError($this->__('This vendor does not allow comments'));
            }
        }
        $this->_redirectReferer();
    }

    public function createAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
    
	public function settingsAction()
    {	
    	$vendorId = (int)Mage::helper('xcentia_vendors')->getDealer()->getId();
    	$vendor	= Mage::getModel('xcentia_vendors/vendor')
            		->setStoreId(Mage::app()->getStore()->getId())
            		->load($vendorId);
        Mage::register('current_vendor', $vendor);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function thanksAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function createpostAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $vendor = Mage::getModel('xcentia_vendors/vendor');
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
                $vendor->setData('status', 0);
				$vendor->setData('allow_comment', 1);
                $vendor->setData('country', $data['country_id']);
                $vendor->setData('state', $data['region_id']);

                $vendor->save();
                Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_vendors')->__('Vendor was successfully saved')
                );
                Mage::getSingleton('customer/session')->setFormData(false);
                $this->_redirect('*/*/thanks');
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_vendors')->__('There was a problem saving the vendor.')
                );
                $this->_redirect('*/*/create');
                return;
            }

        }
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
	
	 public function abusepostAction()
    {
		$id   = $this->getRequest()->getParam('id');
		
		$comment  = Mage::getModel('xcentia_vendors/vendor_comment')->load($id);
		
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		//echo $currentUrl; exit;
		
		//echo '<pre>'; print_r($comment); exit;
		$body="<html>
		<body>
		Hi,<br /> This comment abused by User <br /> 
		<table>
		<tr>
		<th>Comment Id</th>
		<th>User Id</th>
		<th>Comment Title</th>
		<th>Comment</th>
		</tr>
		<tr>
		<td>".$comment['comment_id']."</td>
		<td>".$comment['vendor_id']."</td>
		<td>".$comment['title']."</td>
		<td>".$comment['comment']."</td>
		</tr>
		</table>
		<br /> Thanks
		</body>
		<html>";
		$admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
		$from_name = Mage::getStoreConfig('trans_email/ident_general/name');
		$mail = Mage::getModel('core/email');
		$mail->setToName('Admin');
		$mail->setToEmail($admin_email);
		$mail->setBody($body);
		$mail->setSubject('Post remark as abuse');
		$mail->setFromEmail($admin_email);
		$mail->setFromName($from_name);
		$mail->setType('html');
		$mail->send();

		$this->_redirectReferer();
	}

}
