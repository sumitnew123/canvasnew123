<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Category extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CATEGORY_URL_SUFFIX          = 'catalog/seo/category_url_suffix';
    const XML_PATH_USE_CATEGORY_CANONICAL_TAG   = 'catalog/seo/category_canonical_tag';
    const XML_PATH_CATEGORY_ROOT_ID             = 'catalog/category/root_id';

    /**
     * Store categories cache
     *
     * @var array
     */
    protected $_storeCategories = array();

    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $_categoryUrlSuffix = array();

    /**
     * Retrieve current store categories
     *
     * @param   boolean|string $sorted
     * @param   boolean $asCollection
     * @return  Varien_Data_Tree_Node_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection|array
     */
    public function getStoreCategories($sorted=false, $asCollection=false, $toLoad=true)
    {
        $parent     = Mage::app()->getStore()->getRootCategoryId();
        $cacheKey   = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return new Varien_Data_Collection();
            }
            return array();
        }

        $recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }

    /**
     * Retrieve category url
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return  string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            return $category->getUrl();
        }
        return Mage::getModel('catalog/category')
            ->setData($category->getData())
            ->getUrl();
    }

    /**
     * Check if a category can be shown
     *
     * @param  Mage_Catalog_Model_Category|int $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
        if (!$category->isInRootCategoryList()) {
            return false;
        }

        return true;
    }

/**
     * Retrieve category rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_categoryUrlSuffix[$storeId])) {
            $this->_categoryUrlSuffix[$storeId] = Mage::getStoreConfig(self::XML_PATH_CATEGORY_URL_SUFFIX, $storeId);
        }
        return $this->_categoryUrlSuffix[$storeId];
    }

    /**
     * Retrieve clear url for category as parrent
     *
     * @param string $url
     * @param bool $slash
     * @param int $storeId
     *
     * @return string
     */
    public function getCategoryUrlPath($urlPath, $slash = false, $storeId = null)
    {
        if (!$this->getCategoryUrlSuffix($storeId)) {
            return $urlPath;
        }

        if ($slash) {
            $regexp     = '#('.preg_quote($this->getCategoryUrlSuffix($storeId), '#').')/$#i';
            $replace    = '/';
        }
        else {
            $regexp     = '#('.preg_quote($this->getCategoryUrlSuffix($storeId), '#').')$#i';
            $replace    = '';
        }

        return preg_replace($regexp, $replace, $urlPath);
    }

    /**
     * Check if <link rel="canonical"> can be used for category
     *
     * @param $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_USE_CATEGORY_CANONICAL_TAG, $store);
    }
    
    public function getCategoryMenu($top = 2, $split = true, $groupclass = '', $filters = false, $showcount = true) {
    	$cache = Mage::getSingleton('core/cache');
  		$key = 'collection-category-dropdown_'.http_build_query(func_get_args());
  		if(true || !$data = $cache->load($key)){
  			$rootcategory = Mage::getModel('catalog/category')->load($top);
	    	$categoryCollection= $rootcategory->getChildrenCategories();
			$categoryCollection->addIsActiveFilter();
			$categoryCollection->load();
			//echo $categoryCollection->getSelect(); exit;
			$leftHtml = ''; $rightHtml = '';
			$leftcount = 0; $rightcount = 0;
			list($curUrl, $query) = explode('?', Mage::helper('core/url')->getCurrentUrl());
			foreach ($categoryCollection as $category) {
				if($category->getName() != 'Collections') {
					if($category->getProductCount() > 0) {
						$count = 1; $html = '<ul'.$groupclass.'>';
						$html .= '<h5><a onclick="location.href=\''.($filters ? $curUrl.'?cat='.$category->getId():$category->getUrl()).'\'" href="'.($filters ? $curUrl.'?cat='.$category->getId():$category->getUrl()).'">'.$category->getName() . ($showcount ? ' ('.$category->getProductCount().')':'').'</a></h5>';
						$subcats = Mage::getResourceModel('catalog/category_collection')
			                     ->addAttributeToSelect('name')
			                     ->addAttributeToSelect('url')
			                     ->addAttributeToFilter('entity_id', explode(',', $category->getChildren()))
			                     ->addIsActiveFilter();
			            foreach($subcats as $subcat) {
			            	if($subcat->getProductCount() > 0) {
			            		$html .= '<li><a onclick="location.href=\''.($filters ? $curUrl.'?cat='.$subcat->getId():$subcat->getUrl()).'\'" href="'.($filters ? $curUrl.'?cat='.$subcat->getId():$subcat->getUrl()).'">'.$subcat->getName().'</a></li>';
			            	}
			            	$count++;
			            }
			            $html .= '</ul>';

						if($leftcount > $rightcount) {
							$rightHtml .= $html;
							$rightcount += $count;
						} else {
							$leftHtml .= $html;
							$leftcount += $count;
						}
					}
				}
			}
			if($split) {
				$finalhtml = '<li>
							<div class="dropdown-categories-left">
								<div class="dropdown-categories-menubar">
								'.$leftHtml.'
								</div>
							</div>
						</li>
						<li>
							<div class="dropdown-categories-right">
								<div class="dropdown-categories-menubar">
								'.$rightHtml.'
								</div>
							</div>
						</li>';
			} else {
				$finalhtml = $leftHtml . $rightHtml;
			}
			
			$cache->save($finalhtml, $key, array($key), 60*60*24);
			return $finalhtml;
  		} else {
  			return $data;
  		}
    }
    
    
	public function getDescribeCategory() {
    	$cache = Mage::getSingleton('core/cache');
    	$top = 28;
  		$key = 'collection-category-dropdown-describe-page';
  		if(true || !$data = $cache->load($key)){
  			$rootcategory = Mage::getModel('catalog/category')->load($top);
	    	$categoryCollection = $rootcategory->getChildrenCategories();
			$categoryCollection->addIsActiveFilter();
			$categoryCollection->addAttributeToSelect('image');
			$categoryCollection->load();
			$html = '';
			foreach ($categoryCollection as $category) {
				$category = Mage::getModel('catalog/category')->load($category->getId());
				if($category->getImage() != '') {
				$html .= '<div class="col-md-2">
							<div class="container-inner">
								<div class="container-inner-section">
									<div class="describe-image">
										<a href="'.$category->getUrl().'"><img class="img-responsive" src="'.Mage::getBaseUrl('media').'catalog/category/'.$category->getImage().'" alt="'.$category->getName().'"></a>
									</div>
									<a class="label-text" href="'.$category->getUrl().'">'.$category->getName().'</a>
								</div>
							</div>
						</div>';
				}
				$subcats = Mage::getResourceModel('catalog/category_collection')
	                     ->addAttributeToSelect('name')
	                     ->addAttributeToSelect('url')
	                     ->addAttributeToSelect('image')
	                     ->addAttributeToFilter('entity_id', explode(',', $category->getChildren()))
	                     ->addIsActiveFilter();
	            foreach($subcats as $subcat) {
	            	if($subcat->getImage() != '') {
	            	$html .= '<div class="col-md-2">
								<div class="container-inner">
									<div class="container-inner-section">
										<div class="describe-image">
											<a href="'.$subcat->getUrl().'"><img class="img-responsive" src="'.Mage::getBaseUrl('media').'catalog/category/'.$subcat->getImage().'" alt="'.$subcat->getName().'"></a>
										</div>
										<a class="label-text" href="'.$subcat->getUrl().'">'.$subcat->getName().'</a>
									</div>
								</div>
							</div>';
	            	}
	            }
			}
			//$cache->save($html, $key, array($key), 60*60*24);
			return $html;
  		} else {
  			return $data;
  		}
    }
}
