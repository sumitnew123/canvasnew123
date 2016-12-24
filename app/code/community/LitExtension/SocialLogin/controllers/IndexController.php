<?php
/**
 * @project     SocialLogin
 * @package     LitExtension_SocialLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_SocialLogin_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction(){		Mage::getSingleton('core/session')->setApplogin(true);
        $this->loadLayout();
//        $this->getLayout()->getBlock('root')->setTemplate('le_sociallogin/fix.phtml');
        $this->renderLayout();
        return $this;
    }    	public function customerAction(){		$customer =  Mage::getSingleton('customer/session')->getCustomer()->getId();		echo '            <script type="text/javascript">                window.parent.customerLoggedIn('.$customer.');                window.close();            </script>            ';    }
}