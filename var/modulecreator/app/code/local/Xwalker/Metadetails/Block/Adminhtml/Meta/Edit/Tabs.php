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
 * Meta admin edit tabs
 *
 * @category    Xwalker
 * @package     Xwalker_Metadetails
 * @author      Ultimate Module Creator
 */
class Xwalker_Metadetails_Block_Adminhtml_Meta_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('meta_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xwalker_metadetails')->__('Meta'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Xwalker_Metadetails_Block_Adminhtml_Meta_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_meta',
            array(
                'label'   => Mage::helper('xwalker_metadetails')->__('Meta'),
                'title'   => Mage::helper('xwalker_metadetails')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'xwalker_metadetails/adminhtml_meta_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve meta entity
     *
     * @access public
     * @return Xwalker_Metadetails_Model_Meta
     * @author Ultimate Module Creator
     */
    public function getMeta()
    {
        return Mage::registry('current_meta');
    }
}
