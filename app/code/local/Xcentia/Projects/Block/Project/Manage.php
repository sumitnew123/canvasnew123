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
class Xcentia_Projects_Block_Project_Manage extends Mage_Core_Block_Template
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
    
    public function getVendorList($project) {
    	$vendor_bids = Mage::getModel('xcentia_projects/bid')
    						->getCollection()
    						->addFieldToFilter('project_id', $project->getId())
    						//->addFieldToFilter('sender_id', array('neq'=> $project->getOwnerId()))
    						;
    	foreach($vendor_bids as $bid) {
    		$vendorIds[$bid->getVendorId()] = $bid->getVendorId();
    	}
    	$vendors = Mage::getModel('xcentia_vendors/vendor')
    				->getCollection()
    				->addFieldToFilter('entity_id', array('in'=>$vendorIds));
    	return $vendors;
    }
    
	public function getMessages($project, $vendorlist, $public = true, $with = 0, $lastupdated = 0) {
    	$messages = Mage::getModel('xcentia_projects/message')
    						->getCollection()
    						->addFieldToFilter('project_id', $project->getId());
    		if($lastupdated > 0) {
    			$messages->addFieldToFilter('created_at', array('gt' => date("Y-m-d H:i:s", $lastupdated) ) );
    		}
    		if($public) {
    			$messages->addFieldToFilter('is_private', 0);
    		} else {
    			$messages->addFieldToFilter('is_private', 1);
    			$messages->getSelect()->where(new Zend_Db_Expr("((`recipient_id` = ".$with." OR (`recipient_id` = ".Mage::helper('customer')->getCustomer()->getId()." AND `sender_id` = ".$with.")))"));
    			$messages->load();
    		}
    		//echo $messages->getSelect();
			$date = '';
			foreach($messages as $message) {
				$isme = false; 
				if($message->getSenderId() == Mage::helper('customer')->getCustomer()->getId()) $isme = true;
					$newdate = date("D, jS M", Mage::getModel('core/date')->timestamp(strtotime($message->getCreatedAt()))); 
					if($date != $newdate && $lastupdated == 0) {
						$date = $newdate;
						echo '<div class="conversation-start"><span>'.$date.'</span></div>';
					}
				?>
				<?php if($message->getMessage() != '') { ?>
				<div class="bubble <?php echo $isme ? 'me': 'you' ?>">
				<?php if($public && !$isme && array_key_exists($message->getSenderId(), $vendorlist)) { ?>
					<span class="time"><img src="<?php echo Mage::helper('xcentia_vendors/vendor_image')->init($vendorlist[$message->getSenderId()], 'logo')->keepFrame(false)->resize(100, 100); ?>" class="img-circle" style="width: 40px;" /></span>
				<?php } ?>
				<?php echo $message->getMessage() ?><br /><span class="size-11"><?php echo date("h:iA", Mage::getModel('core/date')->timestamp(strtotime($message->getCreatedAt()))) ?></span></div>
				<?php } ?>
					<?php if($message->getHasAttachment()) {
						$attachments = Mage::getModel('xcentia_projects/attachment')
						->getCollection()
						->addFieldToFilter('message_id', $message->getId());
						foreach($attachments as $attachment) {
							echo '<div class="bubble '.( $isme ? 'me': 'you').'">';
							switch($attachment->getType()) {
								case 'docx':
								case 'doc':
									echo '<i class="fa fa-file-word-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'xls':
								case 'xlsx':
									echo '<i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'pdf':
									echo '<i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'ppt':
								case 'pptx':
									echo '<i class="fa fa-file-powerpoint-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'zip':
								case 'rar':
								case '7z':
								case 'gz':
									echo '<i class="fa fa-file-archive-o fa-2x" aria-hidden="true"></i> ';
									break;
								case 'jpg':
								case 'jpeg':
								case 'png':
								case 'gif':
								case 'bmp':
								case 'tiff':
									echo '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachment/file/'.$attachment->getFilename().'" class="img-responsive" style="width: 50%;"><br>';
									break;
								default:
									echo '<i class="fa fa-file-o fa-2x" aria-hidden="true"></i> ';
									break;
							}
							?>
							<?php echo $attachment->getName() ?>
					<a href="<?php echo $this->getUrl('*/*/downloadattachment', array('id'=>$attachment->getId())) ?>"
						target="_blank"><i class="fa fa-download fa-2x" aria-hidden="true"></i>
					</a></div>
					<?php 	}
					}
					?>
				
			<?php } ?>
			<?php if($lastupdated == 0) { ?>
				<div class="panel-body" style="display: none;">
                	<div id="mulitplefileuploader-<?php echo $with ?>">Select files to upload</div>
                </div>
                <script>
					jQuery(document).ready(function($){
						initiateUpload('mulitplefileuploader-<?php echo $with ?>', '<?php echo $project->getId() ?>', '<?php echo $public ? '0': '1'?>', '<?php echo $with ?>');
					});
				</script>
			<?php } ?>
			<?php
			/*
    	return $messages;
    	
    	$bids = Mage::getModel('xcentia_projects/bid')->getCollection()
        		->addFieldToFilter('project_id', $project->getId());
        $mixed  = array();
		foreach($messages as $message) {
			$mixed[$message->getUpdatedAt()] = $message;
		}
		foreach($bids as $bid) {
			$mixed[$bid->getUpdatedAt()] = $bid;
		}
		ksort($mixed);
		return $mixed;*/
    }
    
    public function getAllBids($project) {
    	$bids = Mage::getModel('xcentia_projects/bid')->getCollection()
        		->addFieldToFilter('project_id', $project->getId());
    	return $bids;
    }
    
    public function getProjectImage($project) {
    	if($project->getType() == 'design') {
    		$design = Mage::getModel('pdp/customerdesign')->load($project->getDesignId());
    		$thumbnails = Mage::helper("pdp")->getThumbnailImage($design->getFilename());
    		foreach($thumbnails as $thumbnail) {
    			return $thumbnail['image'];
    		}
    	} else if($project->getType() == 'travel') {
    		$options = json_decode($project->getOptions());
    		$html = '<div id="map" style="height:250px"></div>
			<script>
			      function initMap() {      	
			        var directionsService = new google.maps.DirectionsService;
			        var directionsDisplay = new google.maps.DirectionsRenderer;
			        var map = new google.maps.Map(document.getElementById(\'map\'), {
			          zoom: 10,
			        });
			        directionsDisplay.setMap(map);
			        calculateAndDisplayRoute(directionsService, directionsDisplay);        
			      }
			
			      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
			        directionsService.route({
			          origin: "'.$options->Origin.'",
			          destination: "'.$options->Destination.'",
			          travelMode: \'DRIVING\'
			        }, function(response, status) {
			          if (status === \'OK\') {
			            directionsDisplay.setDirections(response);
			          }
			        });
			      }
			    </script>
				<script async type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGSD9ez6WVEj1PQIeUX0xKStMim2eT-OA&callback=initMap"></script>';
    		return $html;
    	}
    }
    
    public function getLowestBidVendor() {
    	$bid = Mage::registry('lowest_bid');
    	return $this->getBidVendor($bid);
    }
    
	public function getBidVendor($bid) {
    	$vendor = Mage::getModel('xcentia_vendors/vendor')->load($bid->getVendorId());
    	return '<a target="_blank" href="'.$vendor->getVendorUrl().'">'.$vendor->getTitle().'</a>';
    }
}
