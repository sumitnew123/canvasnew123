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
 * Coverage abstract REST API handler model
 *
 * @category    Xcentia
 * @package     Xcentia_Vendors
 * @author      Ultimate Module Creator
 */
abstract class Xcentia_Vendors_Model_Api2_Coverage_Rest extends Xcentia_Vendors_Model_Api2_Coverage
{
    /**
     * current coverage
     */
    protected $_coverage;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Ultimate Module Creator
     */
    protected function _retrieve() {
        $coverage = $this->_getCoverage();
        $this->_prepareCoverageForResponse($coverage);
        return $coverage->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('xcentia_vendors/coverage_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addFieldToFilter('status', array('eq' => 1));
        $this->_applyCollectionModifiers($collection);
        $coverages = $collection->load();
        $coverages->walk('afterLoad');
        foreach ($coverages as $coverage) {
            $this->_setCoverage($coverage);
            $this->_prepareCoverageForResponse($coverage);
        }
        $coveragesArray = $coverages->toArray();
        $coveragesArray = $coveragesArray['items'];

        return $coveragesArray;
    }

    /**
     * prepare coverage for response
     *
     * @access protected
     * @param Xcentia_Vendors_Model_Coverage $coverage
     * @author Ultimate Module Creator
     */
    protected function _prepareCoverageForResponse(Xcentia_Vendors_Model_Coverage $coverage) {
        $coverageData = $coverage->getData();
    }

    /**
     * create coverage
     *
     * @access protected
     * @param array $data
     * @return string|void
     * @author Ultimate Module Creator
     */
    protected function _create(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * update coverage
     *
     * @access protected
     * @param array $data
     * @author Ultimate Module Creator
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete coverage
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current coverage
     *
     * @access protected
     * @param Xcentia_Vendors_Model_Coverage $coverage
     * @author Ultimate Module Creator
     */
    protected function _setCoverage(Xcentia_Vendors_Model_Coverage $coverage) {
        $this->_coverage = $coverage;
    }

    /**
     * get current coverage
     *
     * @access protected
     * @return Xcentia_Vendors_Model_Coverage
     * @author Ultimate Module Creator
     */
    protected function _getCoverage() {
        if (is_null($this->_coverage)) {
            $coverageId = $this->getRequest()->getParam('id');
            $coverage = Mage::getModel('xcentia_vendors/coverage');
            $coverage->load($coverageId);
            if (!($coverage->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_coverage = $coverage;
        }
        return $this->_coverage;
    }
}
