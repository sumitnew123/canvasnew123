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
 * Message front contrller
 *
 * @category    Xcentia
 * @package     Xcentia_Messages
 * @author      Ultimate Module Creator
 */
class Xcentia_Messages_MessageController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
	 const SEND_MESSAGE='message_template';
	 const MESSAGE_REPLY='reply_message_template';
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('xcentia_messages/message')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('xcentia_messages')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'messages',
                    array(
                        'label' => Mage::helper('xcentia_messages')->__('Messages'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_messages/message')->getMessagesUrl());
        }
        $this->renderLayout();
    }
	
	public function preDispatch()
    {
        parent::preDispatch();
        if (!in_array($this->getRequest()->getActionName(), array('ajaxlist','ajaxmessageslist'))  && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * init Message
     *
     * @access protected
     * @return Xcentia_Messages_Model_Message
     * @author Ultimate Module Creator
     */
    protected function _initMessage()
    {
        $messageId   = $this->getRequest()->getParam('id', 0);
        $message     = Mage::getModel('xcentia_messages/message')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($messageId);
        return $message;
    }

    /**
     * view message action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
	 public function newAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('xcentia_messages/message')->getMessagesUrl());
        }
        $this->renderLayout();
    }
		
	public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
				$customer = Mage::getSingleton('customer/session')->getCustomer();
                $message = $this->_initMessage();
                $message->addData($data);
				if($customer->getGroupId()=='2'){
					$message->setOwner('2');
					$cust=Mage::getModel('customer/customer')->load($data['cust_id']);
					$recepientName=$cust->getFirstname();
					$recepientEmail=$cust->getEmail();
				}else{
					$message->setOwner('1');
					$vendor=Mage::getModel('customer/customer')->load($data['vendor_id']);
					$recepientName=$vendor->getFirstname();
					$recepientEmail=$vendor->getEmail();
				}
				$message->setIsRead('0');
				$message->setStatus('1');
                $message->save();
				$msg=Mage::getModel('xcentia_messages/message')->load($message->getId());
				$msg->setParentId($message->getId());
				$msg->save();
				if(count($_FILES['attachment']['name']) >0){
					for ($i = 0; $i < count($_FILES['attachment']['name']); $i++) {
						$name=basename($_FILES['attachment']['name'][$i]);
						$fileName = $_FILES['attachment']['name'][$i];
						$newFileName = strtotime("now").'-'.rand(1,999).'.'.substr($fileName, strrpos($fileName, '.')+1);
						$target_path = Mage::getBaseDir('media').'/attachment/file/'.$newFileName;
						move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $target_path);
						$attachdata=array("message_id"=>$msg->getId(),"name"=>$name,"file"=>$newFileName,"msg_id"=>$msg->getId(),"status"=>"1");
						$attachment=Mage::getModel('xcentia_messages/attachment');
						$attachment->addData($attachdata);
						$attachment->save();
					}
					$msg->setHasAttachment('1');
					$msg->save();
				}
                Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_messages')->__('Message was successfully saved')
                );
				$vars = array(
								'r_name'=>$recepientName,
								"msg_url"=>Mage::getUrl('messages/message/view',array("id"=>$msg->getId())),
								'logo_url' => Mage::getDesign()->getSkinUrl('images/logo2-white-allpages.png'),
								'store_url'=>Mage::getUrl()
                			);
				Mage::helper('xcentia_messages/message')->sendMail(self::SEND_MESSAGE, $recepientEmail, $recepientName, $vars);
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
                Mage::getSingleton('customer/session')->setMessageData($data);
                $this->_redirect('*/*/new', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was a problem saving the message.')
                );
                Mage::getSingleton('customer/session')->setMessageData($data);
                $this->_redirect('*/*/new', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('customer/session')->addError(
            Mage::helper('xcentia_messages')->__('Unable to find message to save.')
        );
        $this->_redirect('*/*/');
    }
	public function replyAction()
    {
		if ($data = $this->getRequest()->getPost()) {
			try 
			{ 
				$customer = Mage::getSingleton('customer/session')->getCustomer();
				$message = $this->_initMessage()->toArray();
				unset($message['entity_id']);unset($message['updated_at']);unset($message['created_at']);
				if($customer->getGroupId()=='2'){
						$message['owner']='2';
					$cust=Mage::getModel('customer/customer')->load($message['cust_id']);
						$recepientName=$cust->getFirstname();
						$recepientEmail=$cust->getEmail();
				}else{
					$message['owner']='1';
					$vendor=Mage::getModel('customer/customer')->load($message['vendor_id']);
					$recepientName=$vendor->getFirstname();
					$recepientEmail=$vendor->getEmail();
				}
				$message['is_read']='0';
				$message['body']=$data['body'];
				$message['subject']='Re:'.str_replace('Re:','',$message['subject']);
				$msg=Mage::getModel('xcentia_messages/message')->setData($message)->save();	
				 Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_messages')->__('Message was successfully sent')
                );
				$vars = array(
								'r_name'=>$recepientName,
								'logo_url' => Mage::getDesign()->getSkinUrl('images/logo2-white-allpages.png'),
								'store_url'=>Mage::getUrl()
                			);
				Mage::helper('xcentia_messages/message')->sendMail(self::MESSAGE_REPLY, $recepientEmail, $recepientName, $vars);
                $this->_redirect('*/*/');
                return;
			}catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was a problem In message sending.')
                );
                 $this->_redirect('*/*/');
                return;
            }	
		} 
		Mage::getSingleton('customer/session')->addError(
            Mage::helper('xcentia_messages')->__('Unable to find message to save.')
        );
        $this->_redirect('*/*/');
	}
	public function deleteAction(){
		$msgid 	= $this->getRequest()->getParam('id',0);
		try{
			$msg=Mage::getModel('xcentia_messages/message')->load($msgid);
			$msg->setStatus(0)->save();
			Mage::getSingleton('customer/session')->addSuccess(
                    Mage::helper('xcentia_messages')->__('Message Successfully deleted')
                );
			$this->_redirect('*/*/');
			 return;
		} catch (Exception $e) {
			  Mage::logException($e);
              Mage::getSingleton('customer/session')->addError(
                    Mage::helper('xcentia_messages')->__('There was a problem in deleting message.')
                );
		}
	}
    public function viewAction()
    {
        $message = $this->_initMessage();
		$message->setIsRead(1)->save();
        if (!$message) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_message', $message);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('messages-message messages-message' . $message->getId());
        }
       /* if (Mage::helper('xcentia_messages/message')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('xcentia_messages')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'messages',
                    array(
                        'label' => Mage::helper('xcentia_messages')->__('Messages'),
                        'link'  => Mage::helper('xcentia_messages/message')->getMessagesUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'message',
                    array(
                        'label' => $message->getSubject(),
                        'link'  => '',
                    )
                );
            }
        }*/
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $message->getMessageUrl());
        }
        $this->renderLayout();
    }
	
	 public function notificationAction()
    { 
		$this->loadLayout();
		
		$this->renderLayout();
	}
	
	public function ajaxlistAction()
    { 
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$page = $this->getRequest()->getParam('page', 1);
		$messages = Mage::getResourceModel('xcentia_messages/message_collection')
                         ->addFieldToFilter('status', 1);
		if($customer->getGroupId()=='2'){
			$messages->addFieldToFilter('owner', 1);
			$messages->addFieldToFilter('is_read', 0);
			$messages->addFieldToFilter('type', 'invite');
			$messages->addFieldToFilter('vendor_id', $customer->getId());
		}else{
			$messages->addFieldToFilter('owner',2);
			$messages->addFieldToFilter('is_read', 0);
			$messages->addFieldToFilter('type', 'invite');
			$messages->addFieldToFilter('cust_id', $customer->getId());
		}
		$messages->getSelect()->group(array('parent_id'));
        $messages->setPageSize(10)->setCurPage($page)->load();
        ?>  
    <?php foreach ($messages as $_message) : ?>  
		<div class="message-list-item">
			<a href="<?php echo Mage::app()->getStore()->getUrl('messages/message/view',array("id"=>$_message->getId())); ?>">
			 <?php 
				if($_message->getOwner()=='1'){
					$cust=Mage::getModel('customer/customer')->load($_message->getCustId());
					$customerData = Mage::getModel('customer/customer')->load($_message->getCustId())->getData();
					$img = ($customerData['profile_photo'] != '') ? $customerData['profile_photo'] : '';	 
				}else{
					$cust=Mage::getModel('customer/customer')->load($_message->getVendorId());
					$customerData = Mage::getModel('customer/customer')->load($_message->getVendorId())->getData();
					$img = ($customerData['profile_photo'] != '') ? $customerData['profile_photo'] : '';	 
				}
				?>
				<img src="<?php echo Mage::getBaseUrl('media').'profile/'.$img ?>" width="60" height="60" alt="profile image" />
				<?php echo $_message->getSubject();?><?php echo ' '.$_message->getCreatedAt();?></a> <br />
				<?php echo $_message->getBody();?>
			<br clear="all"/>
		</div>
		<br />
    <?php endforeach;?> 
        <?php  
        exit;
	}
	
	public function ajaxmessageslistAction()
    { 
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$page = $this->getRequest()->getParam('page', 1);
		$notification = Mage::getResourceModel('xcentia_messages/message_collection')
                         ->addFieldToFilter('status', 1);
		if($customer->getGroupId()=='2'){
			$notification->addFieldToFilter('owner', 1);
			$notification->addFieldToFilter('is_read', 0);
			$notification->addFieldToFilter('type', 'messages');
			$notification->addFieldToFilter('vendor_id', $customer->getId());
		}else{
			$notification->addFieldToFilter('owner',2);
			$notification->addFieldToFilter('is_read', 0);
			$notification->addFieldToFilter('type', 'messages');
			$notification->addFieldToFilter('cust_id', $customer->getId());
		}
		$notification->getSelect()->group(array('parent_id'));
		  
        $notification->setPageSize(10)->setCurPage($page)->load();
         
         
     foreach ($notification as $_notification) :  
       ?>
		 <div class="message-list-item">
		  <a href="<?php echo Mage::app()->getStore()->getUrl('messages/message/view',array("id"=>$_notification->getId())); ?>">	
		   	  <?php 
				if($_notification->getOwner()=='1'){
					$cust=Mage::getModel('customer/customer')->load($_notification->getCustId());
					$customerData = Mage::getModel('customer/customer')->load($_notification->getCustId())->getData();
					$img = ($customerData['profile_photo'] != '') ? $customerData['profile_photo'] : '';	 
				}else{
					$cust=Mage::getModel('customer/customer')->load($_notification->getVendorId());
					$customerData = Mage::getModel('customer/customer')->load($_notification->getVendorId())->getData();
					$img = ($customerData['profile_photo'] != '') ? $customerData['profile_photo'] : '';	 
				}
				?>
				<img src="<?php echo Mage::getBaseUrl('media').'profile/'.$img ?>" width="60" height="60" alt="profile image" />
				<?php echo $_notification->getSubject();?><?php echo ' '.$_notification->getCreatedAt();?> </a><br /> 
				<?php echo $_notification->getBody();?>
			<br clear="all"/>
		 </div>
      <?php endforeach;    
        exit;
	}
}
