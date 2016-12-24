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
 * Admin search model
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Model_Adminhtml_Search_Attachment extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Xcentia_Projects_Model_Adminhtml_Search_Attachment
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('xcentia_projects/attachment_collection')
            ->addFieldToFilter('name', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $attachment) {
            $arr[] = array(
                'id'          => 'attachment/1/'.$attachment->getId(),
                'type'        => Mage::helper('xcentia_projects')->__('Attachment'),
                'name'        => $attachment->getName(),
                'description' => $attachment->getName(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/projects_attachment/edit',
                    array('id'=>$attachment->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
