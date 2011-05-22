<?php

class BrowseController extends Zend_Controller_Action
{
		protected $_user;
		
    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction() {
    		$userSvc					= Application_Service_User::getInstance();
    		$this->view->user	= $userSvc->getUser(1);
    		
    		$itemSvc					= Application_Service_Item::getInstance();
        $this->view->tree = $itemSvc->getItemTreeForUser($this->view->user, Application_Service_Item::GET_INCLUDE_OFFERS_COUNT);
    }
}
?>