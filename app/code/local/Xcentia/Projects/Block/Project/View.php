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
 * Project view block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Block_Project_View extends Mage_Core_Block_Template
{
    /**
     * get the current project
     *
     * @access public
     * @return mixed (Xcentia_Projects_Model_Project|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentProject()
    {
        return Mage::registry('current_project');
    }
    
	public function getProjectImages($project) {
    	if($project->getType() == 'design') {
    		$design = Mage::getModel('pdp/customerdesign')->load($project->getDesignId());
    		$thumbnails = Mage::helper("pdp")->getThumbnailImage($design->getFilename());
    		return $thumbnails;
    	}
    }
}
