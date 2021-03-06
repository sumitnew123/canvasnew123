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
 * Coverage list block
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author Ultimate Module Creator
 */
class Xcentia_Vendors_Block_Coverage_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $coverages = Mage::getResourceModel('xcentia_vendors/coverage_collection')
                         ->addFieldToFilter('status', 1);
        $coverages->setOrder('city', 'asc');
        $this->setCoverages($coverages);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Vendors_Block_Coverage_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_vendors.coverage.html.pager'
        )
        ->setCollection($this->getCoverages());
        $this->setChild('pager', $pager);
        $this->getCoverages()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
