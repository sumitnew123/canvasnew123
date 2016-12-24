<?php
/**
 * Xwalker_Metadetails extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Xwalker
 * @package        Xwalker_Metadetails
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Meta admin controller
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Adminhtml_Metadetails_MetaController extends Xwalker_Metadetails_Controller_Adminhtml_Metadetails
{
    /**
     * init the meta
     *
     * @access protected
     * @return Xwalker_Metadetails_Model_Meta
     */
    protected function _initMeta()
    {
        $metaId  = (int) $this->getRequest()->getParam('id');
        $meta    = Mage::getModel('xwalker_metadetails/meta');
        if ($metaId) {
            $meta->load($metaId);
        }
        Mage::register('current_meta', $meta);
        return $meta;
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
        $this->_title(Mage::helper('xwalker_metadetails')->__('Meta Details'))
             ->_title(Mage::helper('xwalker_metadetails')->__('Meta'));
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
     * edit meta - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $metaId    = $this->getRequest()->getParam('id');
        $meta      = $this->_initMeta();
        if ($metaId && !$meta->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xwalker_metadetails')->__('This meta no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getMetaData(true);
        if (!empty($data)) {
            $meta->setData($data);
        }
        Mage::register('meta_data', $meta);
        $this->loadLayout();
        $this->_title(Mage::helper('xwalker_metadetails')->__('Meta Details'))
             ->_title(Mage::helper('xwalker_metadetails')->__('Meta'));
        if ($meta->getId()) {
            $this->_title($meta->getUrl());
        } else {
            $this->_title(Mage::helper('xwalker_metadetails')->__('Add meta'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new meta action
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
     * save meta - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('meta')) {
            try {
                $meta = $this->_initMeta();
                $meta->addData($data);
                $meta->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xwalker_metadetails')->__('Meta was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $meta->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setMetaData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xwalker_metadetails')->__('There was a problem saving the meta.')
                );
                Mage::getSingleton('adminhtml/session')->setMetaData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xwalker_metadetails')->__('Unable to find meta to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete meta - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $meta = Mage::getModel('xwalker_metadetails/meta');
                $meta->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xwalker_metadetails')->__('Meta was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xwalker_metadetails')->__('There was an error deleting meta.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xwalker_metadetails')->__('Could not find meta to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete meta - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $metaIds = $this->getRequest()->getParam('meta');
        if (!is_array($metaIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xwalker_metadetails')->__('Please select meta to delete.')
            );
        } else {
            try {
                foreach ($metaIds as $metaId) {
                    $meta = Mage::getModel('xwalker_metadetails/meta');
                    $meta->setId($metaId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xwalker_metadetails')->__('Total of %d meta were successfully deleted.', count($metaIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xwalker_metadetails')->__('There was an error deleting meta.')
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
        $metaIds = $this->getRequest()->getParam('meta');
        if (!is_array($metaIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xwalker_metadetails')->__('Please select meta.')
            );
        } else {
            try {
                foreach ($metaIds as $metaId) {
                $meta = Mage::getSingleton('xwalker_metadetails/meta')->load($metaId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d meta were successfully updated.', count($metaIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xwalker_metadetails')->__('There was an error updating meta.')
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
        $fileName   = 'meta.csv';
        $content    = $this->getLayout()->createBlock('xwalker_metadetails/adminhtml_meta_grid')
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
        $fileName   = 'meta.xls';
        $content    = $this->getLayout()->createBlock('xwalker_metadetails/adminhtml_meta_grid')
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
        $fileName   = 'meta.xml';
        $content    = $this->getLayout()->createBlock('xwalker_metadetails/adminhtml_meta_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xwalker_metadetails/meta');
    }
}
