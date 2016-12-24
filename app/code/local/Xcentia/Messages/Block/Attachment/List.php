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
 * Attachment list block
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author Ultimate Module Creator
 */
class Xcentia_Messages_Block_Attachment_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $attachments = Mage::getResourceModel('xcentia_messages/attachment_collection')
                         ->addFieldToFilter('status', 1);
        $attachments->setOrder('name', 'asc');
        $this->setAttachments($attachments);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Xcentia_Messages_Block_Attachment_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'xcentia_messages.attachment.html.pager'
        )
        ->setCollection($this->getAttachments());
        $this->setChild('pager', $pager);
        $this->getAttachments()->load();
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
