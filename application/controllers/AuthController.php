<?php

class AuthController extends Zend_Controller_Action
{
		protected $_authAdapter;
		protected $_authForm;
		
    public function init()
    {
    		$this->_helper->contextSwitch()
             ->addActionContext('login', 'json')
             ->addActionContext('logut', 'json')
             ->setAutoJsonSerialization(false)
             ->initContext();
    }

		public function preDispatch()
    {
    		if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'user');
            }
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }
	
	  public function indexAction()
    {
    		$this->view->form = $this->getForm();
		}

		public function loginAction()
    {
    		$form = $this->getForm();
    		
    		if (($context = $this->_helper->contextSwitch()->getCurrentContext()) == 'json') {
					$data = Zend_Json::decode($this->getRequest()->getRawBody());
				} else {
					$data = $this->getRequest()->getPost();
				}
        $this->view->username = $data['username'];
        
        if (!$form->isValid($data)) {
        		if ($context == 'json') $this->getResponse()->setHttpResponseCode(400);
            $this->view->success = false;
            $this->view->messages = $form->getMessages();
            $this->view->form = $form;
            return ($context == 'json') ? '' : $this->render('index');
        }

				$authAdapter = $this->getAuthAdapter();
        $authAdapter->setIdentity($form->getValue('username'))
                    ->setCredential($form->getValue('passwd'));

				$auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($authAdapter);
        if (!$result->isValid()) {
        		if ($context == 'json') $this->getResponse()->setHttpResponseCode(401);
            $form->setDescription('Invalid credentials provided');
            $this->view->success = false;
            $this->view->messages = array('Invalid credentials provided');
            $this->view->form = $form;
            return ($context == 'json') ? '' : $this->render('index');
        }

 				$this->view->success = true;
 				if ($context != 'json') {
			  	$this->_redirect('/user/' . Zend_Auth::getInstance()->getIdentity() . '/');
			  }
    }
    
    public function  logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        
        $this->view->success = true;
        if ($context != 'json') {
        	$this->_helper->redirector('index', 'index'); // back to login page
        }
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
		
		public function getForm() {
			if (isset($this->_authForm)) {
				return $this->_authForm;
			}
			
			$this->_authForm = new Application_Form_Auth(array(
            'action' => $this->view->baseUrl('/auth/login'),
            'method' => 'post',
        ));
			
			return $this->_authForm;
		}
		
  
}
?>