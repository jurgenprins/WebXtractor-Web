<?php

class CollectionController extends Zend_Controller_Action
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
    	$this->view->user = Application_Service_User::getInstance()->findUserByUsername(Zend_Auth::getInstance()->getIdentity());
      $this->view->tree = Application_Service_Item::getInstance()->getItemTreeForUser($this->view->user);
    }
    
    public function getCollectionFromRequest() {
      foreach($this->view->tree as $item) {
      	if (is_numeric($this->_getParam('collectionname')) &&
      	    ($item['obj']->getId() == $this->_getParam('collectionname'))) {
      		return $item['obj'];
      	}
      	if ($item['obj']->getName() == $this->_getParam('collectionname')) {
      		return $item['obj'];
      	} 
      }
      throw new Exception('Collection ' . $this->_getParam('collectionname') . ' does not exist') ;
 		}
      
    public function indexAction() {
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
			 	
    	$this->view->collection = $this->getCollectionFromRequest();
    }
    
    public function postAction() {
			$collection = Application_Service_Item::getInstance()->createItem();
			$collectionForm = $this->getItemForm($collection->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			$data['collections']['idparent']	= 0;
			$data['collections']['iduser'] 		= $this->view->user->id;
			
			if (!$collectionForm->isValid($data['collections'])) {
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->messages = $collectionForm->getMessages();
				$this->view->collection = $collection;
				return;
			}
			
			$collection->setOptions($data['collections']);
			
			Application_Service_Item::getInstance()->saveItem($collection);
			
			$this->getResponse()->setHttpResponseCode(201);
        
     	$this->view->success = true;
			$this->view->collection = $collection;
    }
    
    public function putAction() {
			$collection = Application_Service_Item::getInstance()->getItem($this->getCollectionFromRequest());
			$collectionForm = $this->getItemForm($collection->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			$data['collections']['idparent']	= 0;
			$data['collections']['iduser'] 		= $this->view->user->id;
			
			if (!$collectionForm->isValid($data['collections'])) {
				//print_r($collectionForm->getMessages());
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->collection = $collection;
				return;
			}
			
			$collection->setOptions($data['collections']);
		
			Application_Service_Item::getInstance()->saveItem($collection);
		  
     	$this->view->success = true;
			$this->view->collection = $collection;
		}
			
		public function deleteAction() {
			$collection = Application_Service_Item::getInstance()->getItem($this->getCollectionFromRequest());
			
			Application_Service_Item::getInstance()->removeItem($collection);
			
			$this->getResponse()->setHttpResponseCode(204);
			
			$this->view->success = true;
    }

		protected function getItemForm(array $data) {
			$form = new Application_Form_Item();
     	$form->setDefaults($data);
			return $form;
		}
    
}