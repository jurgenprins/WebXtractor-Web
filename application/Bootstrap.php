<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAppAutoload() {
		
		$autoloader = Zend_Loader_Autoloader::getInstance ();
		$autoloader->registerNamespace ( 'WebXtractor_' );
		
		return $autoloader;
	}
	
	protected function _initView()
	{
		$view = new Zend_View();
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()
			->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8')
			->appendHttpEquiv('Content-Language', 'en-US')
			->appendName('keywords', 'extraction, scraping')
			->appendName('description', 'Scrape any webpage into your own collection');
		$view->headTitle('WebXtractor')->setSeparator(' - ');
		$view->env = APPLICATION_ENV;

		$navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml','nav');
		$navContainer = new Zend_Navigation($navContainerConfig);
		$auth       = Zend_Auth::getInstance(); 
		if($auth->hasIdentity()){
			$navContainer->addPage(array(
				'id'		=> 'user_collections',
				'type' 	=> 'uri', 
				'label'	=> 'My Collections',
				'uri'		=> '/user/' . $auth->getIdentity() . '/collections/'));
			$navContainer->addPage(array(
				'id'		=> 'user_profile',
				'type' 	=> 'uri', 
				'label'	=> 'My Profile',
				'uri'		=> '/user/' . $auth->getIdentity() . '/profile/'));
			$navContainer->addPage(array(
				'id'		=> 'auth_logout',
				'type' 	=> 'uri', 
				'label'	=> 'LOGOUT',
				'uri'		=> '/auth/logout/'));
		}
		$view->navigation($navContainer);
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);
		
		return $view;
	}
	
	public function _initRoutes()
	{
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();

		$config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini', 'production');
		$router->addConfig($config, 'routes');
	}
	
	public function _initPlugins()
	{
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new Application_Plugin_HeaderAccept());
		$frontController->registerPlugin(new Application_Plugin_Authenticate());
	}

	public function _initLogger() 
	{
		$r = $this->getPluginResource("log");
		//$f = new Zend_Log_Formatter_Simple('%timestamp% %priorityName% %class%::%function%: %message%' . PHP_EOL);
		Zend_Registry::set('log', $r->getLog());
	}
}
?>