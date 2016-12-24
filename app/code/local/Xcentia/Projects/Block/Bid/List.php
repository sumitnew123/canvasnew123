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
 * Bid list block
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author Ultimate Module Creator
 */
class Xcentia_Projects_Block_Bid_List extends Mage_Core_Block_Template
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
        $bids = Mage::getResourceModel('xcentia_projects/bid_collection')
                         ->addFieldToFilter('status', 1);
        $bids->setOrder('amount', 'asc');
        $this->setBids($bids);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Projects_Block_Bid_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_projects.bid.html.pager'
        )
        ->setCollection($this->getBids());
        $this->setChild('pager', $pager);
        $this->getBids()->load();
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
