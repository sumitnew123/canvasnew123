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
 * Project front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_ProjectController extends Mage_Core_Controller_Front_Action
{
	
	public function preDispatch()
    {
        parent::preDispatch();
        if (!in_array($this->getRequest()->getActionName(), array('create','index','view','ajaxlist'))  && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
    
	public function createAction() {
		$data = $this->getRequest()->getPost();
		//echo '<pre>'; print_r($data); exit;
    	$product = Mage::getModel('catalog/product')->load($data['product']);
    	$projectOptions = array();
    	
		if($data['type'] == 'design') {
	       $design = Mage::getModel('pdp/customerdesign')->load($data['extra_options'], 'filename');
	       $design->setDesignTitle($data['project_name']);
	       $design->save();
	       $data['quantity'] = 0;
	       //if($product->getSku() == 'tshirt') {
	       if(strpos($product->getSku(), 'tshirt') !== false) {
		       	if($data['size_options'] == 'nameandnumbers') {
		       		foreach($data['name'] as $index => $name) {
			       		$data['quantity']++;
			       		$projectOptions['Name-Numbers'] .= $name. ' - ' . $data['number'][$index]. ' - ' . $data['size'][$index] . ', ';
			       	}
			       	$projectOptions['Name-Numbers'] = rtrim($projectOptions['Name-Numbers'], ', ');
		       	} else {
		       		foreach($data['size'] as $size => $qty) {
			       		$data['quantity'] += $qty;
			       		if($qty > 0) $projectOptions['Sizes'] .= $size. ' x ' . $qty . ', ';
			       	}
			       	$projectOptions['Sizes'] = rtrim($projectOptions['Sizes'], ', ');
		       	}
	       }
       	} else if($data['type'] == 'travel') {
       		$data['quantity'] = 0;
       		foreach($data['size'] as $size => $qty) {
				$data['quantity'] += $qty;
				if($qty > 0) $projectOptions['Sizes'] .= $size. ' x ' . $qty . ', ';
			}
			$projectOptions['Sizes'] = rtrim($projectOptions['Sizes'], ', ');
       		$data['project_name'] = str_replace(', India', '', $data['origin']) . ' to ' . str_replace(', India', '', $data['destination']);
       		$projectOptions['Origin'] = $data['origin'];
       		$projectOptions['Destination'] = $data['destination'];
       		$projectOptions['Distance'] = $data['distance'];
       	} else if($data['type'] == '3dobject') {
       		$file = new Varien_Io_File();
       		$directory = Mage::getBaseDir('media').DS.'import'.DS.'png';
       		list($type, $pngdata) = explode(';', $data['3d-image']);
			list(, $pngdata)      = explode(',', $pngdata);
			$pngdata = base64_decode($pngdata);
			$file->write($directory.DS.basename($data['options'][11]).'.png', $pngdata );
       	}
		//echo '<pre>'; print_r($projectOptions); exit;
        if($product->getData('has_options')) {
			foreach($product->getOptions() as $o){
			    $optionType = $o->getType();
			    if ($optionType == 'drop_down') {
			        $values = $o->getValues();
			        foreach ($values as $v) {
			        	if($v->getId() == $data['options'][$o->getId()]) $projectOptions[$o->getTitle()] = $v->getTitle();
			        }
			    } else{
			    	if(!($o->getTitle() == 'Unit' || $o->getTitle() == 'Object File'))
			        $projectOptions[$o->getTitle()] = $data['options'][$o->getId()];
			    }
			}
       }
       
       $expected = date_create($data['expected']['date'] . ' ' . $data['expected']['time'] );
       $bid_end = date_create($data['bid_end']['date'] . ' ' . $data['bid_end']['time'] );

       $project = Mage::getModel('xcentia_projects/project');
       $projData = array('name' => $data['project_name'],
       					'quantity' => $data['quantity'],
       					'budget' => $data['budget'],
       					'expected' => date_format($expected, 'Y-m-d H:i:s'),
       					'bid_end' => date_format($bid_end, 'Y-m-d H:i:s'),
       					'options' => json_encode($projectOptions),
       					'raw_options' => json_encode($data['options']),
       					'type' => $data['type'],
       					'product_id' => $product->getId(),
       					'design_id' => $design ? $design->getId() : 0,
       					'owner_id' => (int)Mage::helper('customer')->getCustomer()->getId(),
       					'is_private' => 0,
       					'is_single' => 0,
       					'status' => 1,
       					'is_live' => 0
      	);
       //echo '<pre>';	print_r($projData); exit;
       $project->addData($projData);
       $project->save();
       if($project->getOwnerId() == 0) {
       		Mage::getSingleton('core/session')->setLatestGuestProject($project->getId());
    		Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getBaseUrl());
    		Mage::getSingleton('customer/session')->addError($this->__('You first need to login/register for saving your project.'));
       		if($data['save'] == 1) {
       			$this->_redirect('customer/account/login', array('referer' => Mage::helper('core')->urlEncode(Mage::getUrl("*/*/manage", array('id' => $project->getId()) ))));
       		} else {
       			$this->_redirect('customer/account/login', array('referer' => Mage::helper('core')->urlEncode(Mage::getUrl("*/*/adoptproject", array('project' => $project->getId()) ))));
       		}
       } else {
       		if($data['save'] == 1) {
       			$this->_redirect('*/*/manage', array('id' => $project->getId()));
       		} else {
       			$this->_redirect('*/*/vendors', array('id' => $project->getId()));
       		}
			
       }
       return;
	}
	
	public function editprojectAction() {
    	$data = $this->getRequest()->getPost();
    	$project = Mage::getModel('xcentia_projects/project')->load($data['project_id']);
    	$expected = date_create($data['expected']['date'] . ' ' . $data['expected']['time'] );
       	$bid_end = date_create($data['bid_end']['date'] . ' ' . $data['bid_end']['time'] );
       	$projData = array('budget' => $data['budget'],
       					'expected' => date_format($expected, 'Y-m-d H:i:s'),
       					'bid_end' => date_format($bid_end, 'Y-m-d H:i:s'),
      	);
    	$project->addData($projData);
        $project->save();   
        $this->_redirect('*/*/manage', array('id' => $data['project_id']));
    }
    
    
	public function adoptprojectAction()
    {
    	if($this->getRequest()->getParam('project') == Mage::getSingleton('core/session')->getLatestGuestProject() ) {
    		$project = Mage::getModel('xcentia_projects/project')->load((int)$this->getRequest()->getParam('project'));
    		$project->setOwnerId(Mage::helper('customer')->getCustomer()->getId())->save();
    		$design = Mage::getModel('pdp/customerdesign')->load(Mage::getSingleton('core/session')->getLatestGuestDesign());
    		$design->setCustomerId($project->getOwnerId())->save();
    		Mage::getSingleton('core/session')->setLatestGuestDesign(0);
    		Mage::getSingleton('core/session')->setLatestGuestProject(0);
    		$this->_redirect('*/*/vendors', array('id' => $project->getId()));
    	}
    }
	public function vendorsAction()
    {
    	$project_id = $this->getRequest()->getParam('id', 0);
    	if($project_id > 0) {
    		$project = Mage::getModel('xcentia_projects/project')->load($project_id);
    		Mage::register('current_project', $project);
    	}
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
    
    public function vendorselectedAction() {
    	$data = $this->getRequest()->getPost();
    	if($data['vendors'][0] == 'all') {
    		$vendorsModels = Mage::getResourceModel('xcentia_vendors/vendor_collection')
                         ->addFieldToFilter('status', 1);
    	} else {
    		$vendorsModels = Mage::getResourceModel('xcentia_vendors/vendor_collection')
                         ->addFieldToFilter('status', 1)
                         ->addFieldToFilter('entity_id', array('in'=>$data['vendors']));
    	}
    	
    	$project = Mage::getModel('xcentia_projects/project')->load($data['project_id']);
    	$project->setIsLive(1)->save();
    	
    	$msgData = array(//'project_id'=>$data['project_id'],
    					 'subject' => 'Invitation for project bid.',
    					 'body' => 'You have been invited to Bid on the project on Bulbandkey.com. Please <a href="'.Mage::getUrl('projects/project/view', array('id'=>$data['project_id'])).'">click here</a> to view details.',
    					 'cust_id' => Mage::helper('customer')->getCustomer()->getId(),
    					 'owner' => Mage::helper('customer')->getCustomer()->getId(),
    					 'project_id' => $data['project_id'],
    					 'has_attachment' => 0,
    					 'is_read' => 0,
    					 'status' => 1,
    					);
    	foreach($vendorsModels as $vendorsModel) {
    		$message = Mage::getModel('xcentia_messages/message');
    		$message->setData($msgData);
    		$message->setVendorId($vendorsModel->getCustomerId());
            $message->save();
            $message->setParentId($message->getId())->save();
        }
        $this->_redirect('*/*/manage', array('id' => $data['project_id']));
    }
    
    
	public function customerAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_projects/project')->getProjectsUrl());
        }
        $this->renderLayout();
    }
    
	public function vendorAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_projects/project')->getProjectsUrl());
        }
        $this->renderLayout();
    }
    
	public function manageAction()
    {
    	$project = $this->_initProject();
        if (!$project) {
            $this->_forward('no-route');
            return;
        }
        $bids = Mage::getModel('xcentia_projects/bid')->getCollection()
        		->addFieldToFilter('project_id', $project->getId())
        		->setOrder('amount','ASC');
        $project->setTotalBids($bids->getSize());
        $project->setLowestBid($bids->getFirstItem()->getAmount());
        if($project->getStatus() == 1 && Mage::getModel('core/date')->timestamp(time()) > strtotime($project->getBidEnd())) {
        	$project->setStatus(0)->save();
        }
        $project->save();
        Mage::register('lowest_bid', $bids->getFirstItem());
        Mage::register('current_project', $project);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_projects/project')->getProjectsUrl());
        }
        $this->renderLayout();
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
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('xcentia_projects/project')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('xcentia_projects')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'projects',
                    array(
                        'label' => Mage::helper('xcentia_projects')->__('Projects'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_projects/project')->getProjectsUrl());
        }
        $this->renderLayout();
    }
	
    public function ajaxlistAction() {
    	$projects = Mage::getResourceModel('xcentia_projects/project_collection');
        $cat = $this->getRequest()->getParam('cat', 0);
        $page = $this->getRequest()->getParam('page', 1);
        if($cat > 0) {
        	$ids = array();
        	$catgory = Mage::getModel('catalog/category')->load($cat);
        	$collections = Mage::getResourceModel('catalog/product_collection')
	        				->addCategoryFilter($catgory);
	        foreach($collections as $product ) $ids[] = $product->getId();
	        $projects->addFieldToFilter('product_id', array('in'=>$ids));
        }
        $projects->setOrder('entity_id', 'desc');
        $projects->setPageSize(10)->setCurPage($page)->load();
        foreach ($projects as $_project) { ?>
        	<tr>
               <td><?php echo Mage::helper('xcentia_projects')->getProjectImage($_project) ?></td>
               <td data-th="Project Details" width="20%"><div class="tbl-details">Project #<?php echo $_project->getId() ?><br><?php echo $_project->getName();?></div></td>
               <td data-th="Description"><div class="tbl-details"><?php $options = json_decode($_project->getOptions()) ?><?php foreach($options as $opname => $opval) { echo $opname.': '.$opval.'<br>'; } ?></div></td>
               <td data-th="Budget"><div class="tbl-details"><?php echo Mage::helper('core')->currency($_project->getBudget()) ?></div></td>
               <td data-th="Lowest Bid"><div class="tbl-details"><?php echo ($_project->getLowestBid()) ? Mage::helper('core')->currency($_project->getLowestBid()) : 'no bids yet' ?></div></td>
               <td data-th="End Time"><div class="tbl-details"><?php echo Mage::helper('core')->formatDate($_project->getBidEnd(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false) ?></div></td>
               <td><div class="tbl-details"><a href="<?php echo $_project->getProjectUrl()?>" class="btn btn-sm btn-info btn-bordered">View Details</a></div></td> 
             </tr>
        <?php }
        exit;
    }
    /**
     * init Project
     *
     * @access protected
     * @return Xcentia_Projects_Model_Project
     * @author Ultimate Module Creator
     */
    protected function _initProject()
    {
        $projectId   = $this->getRequest()->getParam('id', 0);
        $project     = Mage::getModel('xcentia_projects/project')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($projectId);
        if (!$project->getId()) {
            return false;
        }
        return $project;
    }

    /**
     * view project action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $project = $this->_initProject();
        if (!$project) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_project', $project);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('projects-project projects-project' . $project->getId());
        }
        if (Mage::helper('xcentia_projects/project')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('xcentia_projects')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'projects',
                    array(
                        'label' => Mage::helper('xcentia_projects')->__('Projects'),
                        'link'  => Mage::helper('xcentia_projects/project')->getProjectsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'project',
                    array(
                        'label' => $project->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $project->getProjectUrl());
        }
        $this->renderLayout();
    }
    
    public function savemessageAction() {
    	$data = $this->getRequest()->getPost();
    	$message    = Mage::getModel('xcentia_projects/message');
    	$data['sender_id'] = Mage::helper('customer')->getCustomer()->getId();
    	//$data['is_private'] = 0;
    	$data['status'] = 1;
    	$data['has_attachment'] = 0;
    	
    	$message->addData($data);
        $message->save();
        $html = '<div class="bubble me">'.$message->getMessage().'<br /><span class="size-11">'. date("h:iA", Mage::getModel('core/date')->timestamp(strtotime($message->getCreatedAt()))).'</span></div>';
        echo $html;
		exit;
    }
    
	public function updatechatAction() {
    	$type = $this->getRequest()->getParam('type');
    	$lastUpdated = $this->getRequest()->getParam('lastupdated');
    	$project_id = $this->getRequest()->getParam('id');
    	$_project = Mage::getModel('xcentia_projects/project')->load($project_id);
    	
    	$block = new Xcentia_Projects_Block_Project_Manage();
    	$vendors = $block->getVendorList($_project);
    	foreach($vendors as $vendor) {
			$vendorlist[$vendor->getCustomerId()] = $vendor;
    	}
    	echo Mage::getModel('core/date')->gmtTimestamp().'-----';
    	switch($type) {
    		case 'broadcast':
    			$block->getMessages($_project, $vendorlist, true, 0, $lastUpdated);
    			break;
    		case 'customer':
    			$block->getMessages($_project, $vendorlist, false, $_project->getOwnerId(), $lastUpdated);
    			break;
    		default:
    			list($vstr, $vid) = explode('-', $type);
    			$block->getMessages($_project, $vendorlist, false, $vid, $lastUpdated);
    			break;
    	}
		exit;
    }
    
    public function downloadattachmentAction() {
    	$attachment_id = $this->getRequest()->getParam('id', 0);
    	$attachment = Mage::getModel('xcentia_projects/attachment')->load($attachment_id);
    	if($attachment->getId()> 0) {
    		$filepath = Mage::getBaseDir('base').'/media/attachment/file/'.$attachment->getFilename();
    			try {
	                $this->_prepareDownloadResponse($attachment->getName(), array('type' => 'filename', 'value' => $filepath));
	            } catch (Exception $e) {
	                $this->_getSession()->addError($e->getMessage());
	            }
    	}
    }
    
    public function makebidAction() {
    	$data = $this->getRequest()->getPost();
    	$vendor_id = Mage::helper('xcentia_vendors')->getDealer()->getId();
    	$data['vendor_id'] = $vendor_id;
    	$bid = Mage::getModel('xcentia_projects/bid');
    	$collection = $bid->getCollection()->addFieldToFilter('vendor_id', $vendor_id)->addFieldToFilter('project_id', $data['project_id']);
    	$collection->getSelect();
    	$collection->getFirstItem()->getId();
    	
    	if($collection->getSize() > 0) {
    		$bid->load($collection->getFirstItem()->getId());
    	}
    	$bid->addData($data);
        $bid->save();
                
        $this->_redirect('*/*/manage', array('id' => $data['project_id']));
    }
    
	public function acceptbidAction() {
    	$bid_id = $this->getRequest()->getParam('id', 0);
    	$bid = Mage::getModel('xcentia_projects/bid')->load($bid_id);
    	$project = Mage::getModel('xcentia_projects/project')->load($bid->getProjectId());
    	$bid->setSelected(1)->save();
    	$project->setWinner($bid->getVendorId());
    	$project->setWinningBid($bid->getAmount());
    	$project->setStatus(2);
    	$session = Mage::getSingleton('checkout/session');
    	
    	try {
	    	$cart = Mage::getSingleton('checkout/cart');
	    	$cart->truncate();
	    	$product = Mage::getModel('catalog/product')
	                ->setStoreId(Mage::app()->getStore()->getId())
	                ->load($project->getProductId());
	        $product->setPrice($bid->getAmount())->save();
	        $params['qty'] = $project->getQuantity();
	        $params['options'] = json_decode($project->getRawOptions(), true);
	        $session->setQuoteProjectId($project->getId());
	        $session->setQuoteDeliveryDate($bid->getDate());
	        $cart->addProduct($product, $params);
            $cart->save();
			
            $session->setCartWasUpdated(true);
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            
        	$session->setOrderVendorId($bid->getVendorId());
	        $project->save();
	        $this->_redirect('checkout/cart/delivery');
        } catch (Exception $e) {
            $session->addException($e, $this->__('Error while Placing order, please try again later'));
            Mage::logException($e);
            $this->_redirect('*/*/manage', array('id' => $project->getId()));
        }
        return;
    }
    
    public function downloadassetsAction() {
    	$project = $this->_initProject();
    	if($project->getType() == 'design') {
    		$file = new Varien_Io_File();
    		$directory = Mage::getBaseDir('media').DS.'pdp'.DS.'projects'.DS.$project->getId();
    		$file->mkdir($directory);
    		$design = Mage::getModel('pdp/customerdesign')->load($project->getDesignId());
    		$contents = json_decode(Mage::helper('pdp')->getPDPJsonContent($design->getFilename()));
    		$svgs = array();
    		$text = '';
    		foreach($contents as $content) {
    			//echo '<pre>'; print_r($content); echo '</pre>';
    			if ($file->write($directory.DS.$content->label.'.svg', $content->sideSvg )) {
    				$svgs[] = $content->label.'.svg';
			        //$svg = file_get_contents($directory.DS.$content->label.'.svg');
			    }
			    foreach($content->json->objects as $object) {
			    	switch (true){
			    		case ($object->object_type == 'shape'):
			    		case ($object->object_type == 'background'):
			    		case ($object->object_type == 'image'):
			    		case ($object->object_type == 'clipart'):
			    		case ($object->object_type == 'upload_image'):
			    			$file->write($directory.DS.basename($object->isrc), $file->read($object->isrc) );
			    			//echo $directory.DS.basename($object->isrc);
			    			if( end(explode('.', basename($object->isrc) )) == 'svg') {
			    				$svgs[] = basename($object->isrc);
			    			}
			    			break;
			    		case ($object->type == 'text'):
			    		case ($object->type == 'curvedText'):
			    			$text .= '================ TEXT INFO FOR TEXT "'.$object->text.'" ==================='."\r\n";
			    			foreach($object as $attr => $val) {
			    				if($attr == 'fill' OR
			    					$attr == 'stroke' OR
			    					$attr == 'strokeWidth' OR
			    					$attr == 'angle' OR
			    					$attr == 'opacity' OR
			    					$attr == 'shadow' OR
			    					$attr == 'fillRule' OR
			    					$attr == 'text' OR
			    					$attr == 'fontSize' OR
			    					$attr == 'fontWeight' OR
			    					$attr == 'fontFamily' OR
			    					$attr == 'fontStyle' OR
			    					$attr == 'lineHeight' OR
			    					$attr == 'textDecoration' OR
			    					$attr == 'radius' OR
			    					$attr == 'spacing' OR
			    					$attr == 'effect' OR
			    					$attr == 'range' OR
			    					$attr == 'smallFont' OR
			    					$attr == 'largeFont' OR
			    					$attr == 'lineHeight'
			    				)
								$text .= ucfirst($attr).': '.$val."\r\n";
			    			}
			    			break;
			    	}
			    } 
			    
    		}
    		if($text != '') {
			    file_put_contents($directory.DS."TextInfo.txt", $text);
			}
			
			if(sizeof($svgs) > 0) {
				$cmd = '';
				foreach($svgs as $svg) {
					exec('java -jar shell/batik-1.8/batik-rasterizer-1.8.jar "'.$directory.DS.$svg.'"');
				}
			}
    		//$zipname = $directory.DS.'Project-'.$project->getId().'-'.$project->getName().'.zip';
    		$downloadfilename = 'Project-'.$project->getId().'-'.$project->getName().'.zip';
    		$zipname = tempnam(sys_get_temp_dir(), 'zip');
		    $zip = new ZipArchive;
		    $zip->open($zipname, ZIPARCHIVE::OVERWRITE);
		    if ($handle = opendir($directory)) {
		      while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != ".." && !strstr($entry,'.php')) {
		            $zip->addFile($directory.DS.$entry, $entry);
		        }
		      }
		      closedir($handle);
		    }
		    $zip->close();
		    $this->_prepareDownloadResponse($downloadfilename, array('type' => 'filename', 'value' => $zipname));
		    unlink($zipname);
    	} else if($project->getType() == '3dobject') {
    		$options = json_decode($project->getRawOptions());
    		$file = Mage::getBaseDir('media').DS.'import'.DS. $options->{11};
    		$this->_prepareDownloadResponse($options->{11}, array('type' => 'filename', 'value' => $file));
    	}
    	
    }
    
	public function uploadstlAction() {
		 $_FILES['stlfile'] = array('name' => $_FILES['stlfile']['name'], 'type' => $_FILES['stlfile']['type'], 'tmp_name' => $_FILES['stlfile']['tmp_name'], 'error' => $_FILES['stlfile']['error'], 'size' => $_FILES['stlfile']['size'] );
		 try {
				$uploader = new Varien_File_Uploader('stlfile');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(false);
				$uploader->setAllowCreateFolders(true);
				$fileName = $_FILES['stlfile']['name'];
				$newFileName = strtotime("now").'-'.rand(1,999).'.'.substr($fileName, strrpos($fileName, '.')+1);
				$result = $uploader->save( Mage::getBaseDir('media').'/import/', $newFileName);
				Mage::getSingleton('core/session')->set('uploadedstl', $result['file']);
				echo $result['file'];
				exit;
			} 
			catch (Exception $e) {
				echo $e->getMessage();
				exit;
			}
	}
	
	public function uploadattachmentAction() {
		 $_FILES['file'] = array('name' => $_FILES['file']['name'][0], 'type' => $_FILES['file']['type'][0], 'tmp_name' => $_FILES['file']['tmp_name'][0], 'error' => $_FILES['file']['error'][0], 'size' => $_FILES['file']['size'][0] );
			try {
				$uploader = new Varien_File_Uploader('file');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(false);
				$uploader->setAllowCreateFolders(true);
				$fileName = $_FILES['file']['name'];
				$newFileName = strtotime("now").'-'.rand(1,999).'.'.substr($fileName, strrpos($fileName, '.')+1);
				$result = $uploader->save( Mage::getBaseDir('media').'/attachment/file/', $newFileName);
				
				$data = $this->getRequest()->getPost();
		    	$message    = Mage::getModel('xcentia_projects/message');
		    	$data['sender_id'] = Mage::helper('customer')->getCustomer()->getId();
		    	//$data['is_private'] = 0;
		    	$data['status'] = 1;
		    	$data['has_attachment'] = 1;
		    	
		    	$message->addData($data);
		        $message->save();
		        
		        $attachdata = array("message_id"=>$message->getId(),"name"=>$fileName,"filename"=>$result['file'],"type"=>substr($fileName, strrpos($fileName, '.')+1),"status"=>"1");
				$attachment = Mage::getModel('xcentia_projects/attachment');
				$attachment->setData($attachdata);
				$attachment->save();
				
				$html = '<div class="bubble me">';
					switch($attachment->getType()) {
								case 'docx':
								case 'doc':
									$html .=  '<i class="fa fa-file-word-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'xls':
								case 'xlsx':
									$html .=  '<i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'pdf':
									$html .=  '<i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'ppt':
								case 'pptx':
									$html .=  '<i class="fa fa-file-powerpoint-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'zip':
								case 'rar':
								case '7z':
								case 'gz':
									$html .=  '<i class="fa fa-file-archive-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'jpg':
								case 'jpeg':
								case 'png':
								case 'gif':
								case 'bmp':
								case 'tiff':
									$html .=  '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachment/file/'.$attachment->getFilename().'" class="img-responsive" style="width: 50%;"><br>';
									break;
								default:
									$html .=  '<i class="fa fa-file-o fa-2x" aria-hidden="true"></i> ';
									break;
							}
							
				$html .= $attachment->getName();
				$html .= '<a href="'.Mage::getUrl('*/*/downloadattachment', array('id'=>$attachment->getId())) .'" target="_blank"><i class="fa fa-download fa-2x" aria-hidden="true"></i></a></div>';
				echo $html;
				exit;
			} 
			catch (Exception $e) {
				echo $e->getMessage();
				exit;
			}
	}
}