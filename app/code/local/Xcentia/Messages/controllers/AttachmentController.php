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
 * Attachment front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_AttachmentController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('xcentia_messages/attachment')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('xcentia_messages')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'attachments',
                    array(
                        'label' => Mage::helper('xcentia_messages')->__('Attachments'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_messages/attachment')->getAttachmentsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('xcentia_messages/attachment/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('xcentia_messages/attachment/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('xcentia_messages/attachment/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Attachment
     *
     * @access protected
     * @return Xcentia_Messages_Model_Attachment
     * @author Ultimate Module Creator
     */
    protected function _initAttachment()
    {
        $attachmentId   = $this->getRequest()->getParam('id', 0);
        $attachment     = Mage::getModel('xcentia_messages/attachment')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($attachmentId);
        return $attachment;
    }
	public function downloadAction()
    {
		$attachment = $this->_initAttachment();
		
		$file=Mage::getBaseDir('media').'/attachment/file'.'/'.$attachment->getFile();
		$this->loadLayout();
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$newfilename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($newfilename);
			exit;
		}
		$this->renderLayout();
	}

    /**
     * view attachment action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $attachment = $this->_initAttachment();
        if (!$attachment) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_attachment', $attachment);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('messages-attachment messages-attachment' . $attachment->getId());
        }
        if (Mage::helper('xcentia_messages/attachment')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('xcentia_messages')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'attachments',
                    array(
                        'label' => Mage::helper('xcentia_messages')->__('Attachments'),
                        'link'  => Mage::helper('xcentia_messages/attachment')->getAttachmentsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'attachment',
                    array(
                        'label' => $attachment->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $attachment->getAttachmentUrl());
        }
        if ($headBlock) {
            if ($attachment->getMetaTitle()) {
                $headBlock->setTitle($attachment->getMetaTitle());
            } else {
                $headBlock->setTitle($attachment->getName());
            }
            $headBlock->setKeywords($attachment->getMetaKeywords());
            $headBlock->setDescription($attachment->getMetaDescription());
        }
        $this->renderLayout();
    }
}
