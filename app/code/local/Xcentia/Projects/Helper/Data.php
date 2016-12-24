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
 * Projects default helper
 *
 * @category    Xcentia
 * @package     Xcentia_Projects
 * @author      Ultimate Module Creator
 */
class Xcentia_Projects_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
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
    		//return '<img src="'.$this->getSkinUrl('images/map.png').'" class="img-responsive">';
    		return '<img src="https://maps.googleapis.com/maps/api/staticmap?size=200x200&maptype=roadmap&markers=color:green|label:O|'.$options->Origin.'&markers=color:red|label:D|'.$options->Destination.'&path=color:blue|weight:5|'.$options->Origin.'|'.$options->Destination.'&key=AIzaSyBSOCQP0Dj3gMLF7umj7-x7FWeolQyBnIs" class="img-responsive">';
    	} else if($project->getType() == '3dobject') {
    		$options = json_decode($project->getRawOptions());
    		//return '<img src="'.$this->getSkinUrl('images/map.png').'" class="img-responsive">';
    		return '<img src="'.Mage::getBaseUrl('media').'/import/png/'.$options->{11}.'.png" class="img-responsive">';
    	}
    }
    
	public function getProjectImages($project) {
    	if($project->getType() == 'design') {
    		$design = Mage::getModel('pdp/customerdesign')->load($project->getDesignId());
    		$thumbnails = Mage::helper("pdp")->getThumbnailImage($design->getFilename());
    		$html = '';
    		$count = 1;
    		foreach($thumbnails as $thumbnail) {
    			if($count == 1) {
    				$html .= '<div class="col-xs-12 project-main-image">'.$thumbnail['image'].'</div>';
    			}
    			if(sizeof($thumbnails) == 1) return $html;
    			$html .= '<div class="col-xs-3 project-side-image" id="project-side-'.$count.'">'.$thumbnail['image'].'</div>';
    			$count++;
    		}
    		$html .= '<script>jQuery(document).ready(function($){
    			$(\'.project-side-image\').click(function(){
    				$(\'.project-main-image\').html($(this).html());
    			});
    		});</script>';
    		return $html;
    	} else if($project->getType() == 'travel') {
    		$options = json_decode($project->getOptions());
    		$html = '<div id="map" style="height:400px"></div>
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
				<script async type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSOCQP0Dj3gMLF7umj7-x7FWeolQyBnIs&callback=initMap"></script>';
    		return $html;
    	} else if($project->getType() == '3dobject') {
    		$options = json_decode($project->getRawOptions());
    		//return '<img src="'.$this->getSkinUrl('images/map.png').'" class="img-responsive">';
    		return '<img src="'.Mage::getBaseUrl('media').'/import/png/'.$options->{11}.'.png" class="img-responsive">';
    	}
    }
    
    
	public function getProjectImagePng($project) {
    	if($project->getType() == 'design') {
    		$design = Mage::getModel('pdp/customerdesign')->load($project->getDesignId());
    		$thumbnails = Mage::helper("pdp")->getThumbnailImage($design->getFilename());
    		$imagename = 'project-'.$project->getId().'.png';
    		if(file_exists( Mage::getBaseDir('media').DS.'pdp'.DS.'projects'.DS.$imagename )) {
    			return Mage::getBaseUrl('media').'pdp'.DS.'projects'.DS.$imagename;
    		} else {
    			$file = new Varien_Io_File();
    			$svg = Mage::getBaseDir('media').DS.'pdp'.DS.'projects'.DS.'project-'.$project->getId().'.svg';
    			$png = Mage::getBaseDir('media').DS.'pdp'.DS.'projects'.DS.$imagename;
	    		foreach($thumbnails as $thumbnail) {
	    			$file->write($svg, str_replace('http://dev.bulbandkey.com/media', 'file://'.Mage::getBaseDir('media'), $thumbnail['image']));
	    			exec("inkscape -z $svg -e $png -w600 -h600");
	    			return Mage::getBaseUrl('media').'pdp'.DS.'projects'.DS.$imagename;
	    			//return $thumbnail['image'];
	    		}
    		}
    		
    	} else if($project->getType() == 'travel') {
    		$options = json_decode($project->getOptions());
    		//return '<img src="'.$this->getSkinUrl('images/map.png').'" class="img-responsive">';
    		return 'https://maps.googleapis.com/maps/api/staticmap?size=600x600&maptype=roadmap&markers=color:green|label:O|'.$options->Origin.'&markers=color:red|label:D|'.$options->Destination.'&path=color:blue|weight:5|'.$options->Origin.'|'.$options->Destination.'&key=AIzaSyBSOCQP0Dj3gMLF7umj7-x7FWeolQyBnIs';
    	} else if($project->getType() == '3dobject') {
    		$options = json_decode($project->getRawOptions());
    		//return '<img src="'.$this->getSkinUrl('images/map.png').'" class="img-responsive">';
    		return Mage::getBaseUrl('media').'/import/png/'.$options->{11}.'.png';
    	}
    }
}
