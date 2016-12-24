<?php
class MST_Pdp_ProjectController extends Mage_Core_Controller_Front_Action
{
    public function createAction()
    {
        $data = $this->getRequest()->getPost();
    	$product = Mage::getModel('catalog/product')->load($data['product']);
    	$projectOptions = array();
    	//echo $product->getName(); exit;
        if($product->getData('has_options')) {
			foreach($product->getOptions() as $o){
			    $optionType = $o->getType();
			    if ($optionType == 'drop_down') {
			        $values = $o->getValues();
			        foreach ($values as $v) {
			        	if($v->getId() == $data['options'][$o->getId()]) $projectOptions[$o->getTitle()] = $v->getTitle();
			        }
			    }else{
			        $projectOptions[$o->getTitle()] = $data['options'][$o->getId()];
			    }
			}
       }
       $project = Mage::getModel('pdp/customerdesign')->load($data['extra_options'], 'filename');
       $project->setDesignTitle($data['project_name']);
       $project->setQuantity($data['quantity']);
       $project->setBudget($data['budget']);
       $project->setExpected($data['expected']);
       $project->setOptions(json_encode($projectOptions));
       $project->save();
       
       $project->updateDesignDetails($project->getData());
        echo '<pre>'; print_r($data); print_r($project->getData());  exit;
    }
	
}