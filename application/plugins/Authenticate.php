<?php
	class Application_Plugin_Authenticate extends Zend_Controller_Plugin_Abstract { 
		public function preDispatch(Zend_Controller_Request_Abstract $request) {
			if (!$request->getParam('username')
				|| ($request->getControllerName() == 'auth')
				|| (
					($request->getControllerName() == 'user') && 
					in_array($request->getActionName(), array('index','post')) &&
					($request->getMethod() == 'POST'))
				|| ($request->getControllerName() == 'error')) {
				Zend_Registry::get('log')->debug("AUTH SKIP " . $request->getControllerName() . '/' . $request->getActionName() . '/' . $request->getMethod());
				return true;
			}
			
			Zend_Registry::get('log')->debug("AUTH CHECK " . $request->getControllerName() . '/' . $request->getActionName() . '/' . $request->getMethod());
			
			
			$dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
   		
   		$auth       = Zend_Auth::getInstance(); 
			if(!$auth->hasIdentity()){
				switch ($request->getParam('format')) {
					case 'json':
						$this->getResponse()->setHttpResponseCode(401)->sendResponse();
						break;
					default:							
						$this->getResponse()->setRedirect($dispatcher->getFrontController()->getBaseUrl() . '/')->sendResponse();
				}
				exit;
			}
			
			// owner authorization check
			if (strcmp($request->getParam('username'), $auth->getIdentity())) {
				switch ($request->getParam('format')) {
					case 'json':
						$this->getResponse()->setHttpResponseCode(403)->sendResponse();
						break;
					default:							
						$this->getResponse()->setRedirect($dispatcher->getFrontController()->getBaseUrl() . '/user/' . $auth->getIdentity() . '/')->sendResponse();
				}
				exit;
			}
		}
	}
?>