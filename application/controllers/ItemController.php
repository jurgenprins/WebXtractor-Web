<?php

class ItemController extends Zend_Controller_Action
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
    	$itemSvc = Application_Service_Item::getInstance();
    	
    	$this->view->user = Application_Service_User::getInstance()->findUserByUsername(Zend_Auth::getInstance()->getIdentity());
      $this->view->tree = $itemSvc->getItemTreeForUser($this->view->user);
    
    	$selItem = null;
      foreach($this->view->tree as $item) {
      	if (is_numeric($this->_getParam('collectionname')) &&
      	    ($item['obj']->getId() == $this->_getParam('collectionname'))) {
      	  $selItem = $item;
      		break;
      	}
      	if ($item['obj']->getName() == $this->_getParam('collectionname')) {
      		$selItem = $item;
      		break;
      	} 
      }
      if (is_null($selItem)) {
      	throw new Exception('Collection ' . $this->_getParam('collectionname') . ' does not exist') ;
      }
			$this->view->collection = $selItem['obj'];
	
			$start	= $this->_getParam('start') ? (0 + $this->_getParam('start'))				: 0;
			$limit	= $this->_getParam('limit') ? (0 + $this->_getParam('limit'))				: 999;
			$sort		= $this->_getParam('sort') 	? strtolower($this->_getParam('sort'))	: 'id';
			$dir		= $this->_getParam('dir') 	? strtolower($this->_getParam('dir')) 	: 'asc';
			
			$items = array();
			foreach($selItem['children'] as $item) {
				$items[] = $item['obj'];
			}
			$this->view->items  		= array_slice($items, $start, $limit);
			foreach($this->view->items as $idx => $item) {
				$this->view->items[$idx]->setOfferscount($itemSvc->getOfferCountForItem($item));
			}
			
			$this->view->itemscount = count($selItem['children']);
    }
    
    public function getItemFromRequest() {
    	foreach($this->view->items as $item) {
  			if ($item->getId() == $this->_getParam('itemid')) {
  				return $item;		
  			}
  		}
  		throw new Exception('Item ' . $this->_getParam('itemid') . ' does not exist') ;
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
    }
    
    public function postAction() {
			$item = Application_Service_Item::getInstance()->createItem();
			$itemForm = $this->getItemForm($item->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			$data['items']['idparent']	= $this->view->collection->id;
			$data['items']['iduser'] 		= $this->view->user->id;
			
			if (!$itemForm->isValid($data['items'])) {
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->item = $item;
				return;
			}
			
			$item->setOptions($data['items']);
			
			Application_Service_Item::getInstance()->saveItem($item);
			
			$this->getResponse()->setHttpResponseCode(201);
        
     	$this->view->success = true;
			$this->view->item = $item;
			
			//$this->view->location = $this->getRequest()->getBaseUrl() . $this->view->navigation()->findOneById('user_collections')->getUri() . $this->_getParam('collectionname') . '/datasources/' . $datasource->getId();
    }
    
		public function putAction() {
			$item = Application_Service_Item::getInstance()->getItem($this->getItemFromRequest());
			$itemForm = $this->getItemForm($item->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			$data['items']['idparent']	= $this->view->collection->id;
			$data['items']['iduser'] 		= $this->view->user->id;
			
			if (!$itemForm->isValid($data['items'])) {
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->item = $item;
				return;
			}
			
			$item->setOptions($data['items']);
		
		  Application_Service_Item::getInstance()->saveItem($item);
		  
     	$this->view->success = true;
			$this->view->item = $item;
		}
			
		public function deleteAction() {
			$item = Application_Service_Item::getInstance()->getItem($this->getItemFromRequest());
			
			Application_Service_Item::getInstance()->removeItem($item);
			
			$this->getResponse()->setHttpResponseCode(204);
			
			$this->view->success = true;
    }

		protected function getItemForm(array $data) {
			$form = new Application_Form_Item();
     	$form->setDefaults($data);
			return $form;
		}

		public function offersAction() {
    	$this->view->offers		= $itemSvc->getOffersForItem($this->getItemFromRequest());
    }
}

   