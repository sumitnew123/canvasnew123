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
 * Project admin controller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Adminhtml_Projects_ProjectController extends Xcentia_Projects_Controller_Adminhtml_Projects
{
    /**
     * init the project
     *
     * @access protected
     * @return Xcentia_Projects_Model_Project
     */
    protected function _initProject()
    {
        $projectId  = (int) $this->getRequest()->getParam('id');
        $project    = Mage::getModel('xcentia_projects/project');
        if ($projectId) {
            $project->load($projectId);
        }
        Mage::register('current_project', $project);
        return $project;
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
             ->_title(Mage::helper('xcentia_projects')->__('Projects'));
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
     * edit project - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $projectId    = $this->getRequest()->getParam('id');
        $project      = $this->_initProject();
        if ($projectId && !$project->getId()) {
            $this->_getSession()->addError(
                Mage::helper('xcentia_projects')->__('This project no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getProjectData(true);
        if (!empty($data)) {
            $project->setData($data);
        }
        Mage::register('project_data', $project);
        $this->loadLayout();
        $this->_title(Mage::helper('xcentia_projects')->__('Projects'))
             ->_title(Mage::helper('xcentia_projects')->__('Projects'));
        if ($project->getId()) {
            $this->_title($project->getName());
        } else {
            $this->_title(Mage::helper('xcentia_projects')->__('Add project'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new project action
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
     * save project - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('project')) {
            try {
                $data = $this->_filterDates($data, array('expected'));
                $project = $this->_initProject();
                $project->addData($data);
                $project->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Project was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $project->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setProjectData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was a problem saving the project.')
                );
                Mage::getSingleton('adminhtml/session')->setProjectData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_projects')->__('Unable to find project to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete project - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $project = Mage::getModel('xcentia_projects/project');
                $project->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Project was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error deleting project.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('xcentia_projects')->__('Could not find project to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete project - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $projectIds = $this->getRequest()->getParam('project');
        if (!is_array($projectIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select projects to delete.')
            );
        } else {
            try {
                foreach ($projectIds as $projectId) {
                    $project = Mage::getModel('xcentia_projects/project');
                    $project->setId($projectId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('xcentia_projects')->__('Total of %d projects were successfully deleted.', count($projectIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error deleting projects.')
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
        $projectIds = $this->getRequest()->getParam('project');
        if (!is_array($projectIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select projects.')
            );
        } else {
            try {
                foreach ($projectIds as $projectId) {
                $project = Mage::getSingleton('xcentia_projects/project')->load($projectId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d projects were successfully updated.', count($projectIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating projects.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Is Private change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massIsPrivateAction()
    {
        $projectIds = $this->getRequest()->getParam('project');
        if (!is_array($projectIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select projects.')
            );
        } else {
            try {
                foreach ($projectIds as $projectId) {
                $project = Mage::getSingleton('xcentia_projects/project')->load($projectId)
                    ->setIsPrivate($this->getRequest()->getParam('flag_is_private'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d projects were successfully updated.', count($projectIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating projects.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Is Single change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massIsSingleAction()
    {
        $projectIds = $this->getRequest()->getParam('project');
        if (!is_array($projectIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('xcentia_projects')->__('Please select projects.')
            );
        } else {
            try {
                foreach ($projectIds as $projectId) {
                $project = Mage::getSingleton('xcentia_projects/project')->load($projectId)
                    ->setIsSingle($this->getRequest()->getParam('flag_is_single'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d projects were successfully updated.', count($projectIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('xcentia_projects')->__('There was an error updating projects.')
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
        $fileName   = 'project.csv';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_project_grid')
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
        $fileName   = 'project.xls';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_project_grid')
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
        $fileName   = 'project.xml';
        $content    = $this->getLayout()->createBlock('xcentia_projects/adminhtml_project_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('xcentia_projects/project');
    }
}
