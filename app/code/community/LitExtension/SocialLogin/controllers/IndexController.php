<?php
/**
 * @project     SocialLogin
 * @package     LitExtension_SocialLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_SocialLogin_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction(){
        $this->loadLayout();
//        $this->getLayout()->getBlock('root')->setTemplate('le_sociallogin/fix.phtml');
        $this->renderLayout();
        return $this;
    }
}