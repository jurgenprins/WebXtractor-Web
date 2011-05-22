<?php

class UserController extends Zend_Controller_Action
{
		public function init()
    {
        $this->_helper->contextSwitch()
             ->addActionContext('index', 'json')
             ->addActionContext('post', 'json')
             ->addActionContext('put', 'json')
             ->addActionContext('delete', 'json')
             ->setAutoJsonSerialization(false)
             ->initContext();
    }

		public function preDispatch()
    {
    	if (in_array($this->getRequest()->getActionName(), array('index','post')) &&
					($this->getRequest()->getMethod() == 'POST')) {
				return;
			}
				
			if (!Zend_Auth::getInstance()->getIdentity()) {
				$this->_forward('index', 'index');
				return;
			}
			
    	$this->view->user = Application_Service_User::getInstance()->findUserByUsername(Zend_Auth::getInstance()->getIdentity());
    	$this->view->tree = Application_Service_Item::getInstance()->getItemTreeForUser($this->view->user);
    }
    
    public function indexAction()
    {
    	// for GET, the preDispatcher returns everything we need
			
			if ($this->getRequest()->getMethod() === 'POST') {
		 		return $this->_forward('post');
		 	}
		 	
		}
	
		public function getAction() {
    	if ($this->getRequest()->getMethod() === 'PUT') {
		 		return $this->_forward('put');
		 	}
		 	if ($this->getRequest()->getMethod() === 'DELETE') {
		 		return $this->_forward('delete');
		 	}
		}
		 	
		public function postAction()
    {
			$user = Application_Service_User::getInstance()->createUser();
			$userForm = $this->getUserForm($user->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			
			if (!$userForm->isValid($data)) {
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->messages = $userForm->getMessages();
				$this->view->user = $user;
				return;
			}
			
			if (!is_null(Application_Service_User::getInstance()->findUserByUsername($data['username']))) {
				$this->getResponse()->setHttpResponseCode(409);
				$this->view->success = false;
				$this->view->messages = array('User already exists');
				$this->view->user = $user;
				return;
			}
			
			$user->setOptions($data);
			
			Application_Service_User::getInstance()->saveUser($user);
			
      $authAdapter = $this->getAuthAdapter();
      $authAdapter->setIdentity($user->getUsername())
                  ->setCredential($user->getPasswd());

			$auth    = Zend_Auth::getInstance();
      $result  = $auth->authenticate($authAdapter);

			$this->getResponse()->setHttpResponseCode(201);
      
			$this->view->success = true;
			$this->view->user = $user;
    }
    
    public function putAction() {
			$user = Application_Service_User::getInstance()->getUser($this->view->user);
			$userForm = $this->getUserForm($user->getOptions());
			
			if (!$userForm->isValid($data)) {
				//print_r($collectionForm->getMessages());
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				return;
			}
			
			$user->setOptions($data);
		
			Application_Service_User::getInstance()->saveUser($user);
		  
     	$this->view->success = true;
		}
			
		public function deleteAction() {
			$collection = Application_Service_Item::getInstance()->getItem($this->getCollectionFromRequest());
			
			Application_Service_Item::getInstance()->removeItem($collection);
			
			$this->getResponse()->setHttpResponseCode(204);
			
			$this->view->success = true;
    }
    
    public function getAuthAdapter() {
			if (isset($this->_authAdapter)) {
				return $this->_authAdapter;
			}
			
			$userSvc		= Application_Service_User::getInstance();
      $userDao		= $userSvc->getUserDao();
      $userTbl		= $userDao->getDbTable();
      	  
			$this->_authAdapter = new Zend_Auth_Adapter_DbTable($userTbl->getAdapter(), 'user', 'username', 'passwd'/* , "MD5(?)"*/);
			
			return $this->_authAdapter;
		}
		
    protected function getUserForm(array $data) {
			$form = new Application_Form_User();
     	$form->setDefaults($data);
			return $form;
		}
}
?>