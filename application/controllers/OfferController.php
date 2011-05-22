<?php

class OfferController extends Zend_Controller_Action
{
		public function init()
    {
    		$contextSwitch = Zend_Controller_Action_HelperBroker::getStaticHelper('ContextSwitch');
				if(!$contextSwitch->hasContext('atom')){
        	$contextSwitch->addContext('atom',array(
                                'suffix'        => 'feed',
                                'headers'       => array('Content-Type' => 'application/atom+xml')));
        }

				$contextSwitch = $this->_helper->getHelper('contextSwitch');
				$contextSwitch
        		 ->addActionContext('index', 'json')
        		 ->addActionContext('index', 'atom')
        		 ->addActionContext('update', 'json')
             ->setAutoJsonSerialization(false)
             ->initContext();
    }
    
    public function preDispatch() {
			$this->view->user = Application_Service_User::getInstance()->findUserByUsername(Zend_Auth::getInstance()->getIdentity());
      $this->view->tree = Application_Service_Item::getInstance()->getItemTreeForUser($this->view->user);
      
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
			$sort		= $this->_getParam('sort') 	? strtolower($this->_getParam('sort'))	: 'updated';
			$dir		= $this->_getParam('dir') 	? strtolower($this->_getParam('dir')) 	: 'desc';
			$query	= $this->_getParam('query')	? $this->_getParam('query')							: '';
			$onlynew= $this->_getParam('new')						? (0 + $this->_getParam('new'))			: 0;
			$rank		= strlen($this->_getParam('rank'))	? (0 + $this->_getParam('rank'))		: -1;
			$rankup	= $this->_getParam('rankup')				? (0 + $this->_getParam('rankup'))	: 0;
		
			if ($this->_getParam('itemid')) {
				foreach($selItem['children'] as $item) {
					if ($item['obj']->getId() == $this->_getParam('itemid')) {
						$this->view->offerscount	= Application_Service_Item::getInstance()->getOfferCountForItem($item['obj'], $query, $onlynew, $rank, $rankup);
						$this->view->offers				= Application_Service_Item::getInstance()->getOfferListForItem($item['obj'], $start, $limit, $sort, $dir, $query, $onlynew, $rank, $rankup);
					}
				}	
				return;
			}
			
			$this->view->offerscount	= Application_Service_Item::getInstance()->getOfferCountForItem($this->view->collection, $query, $onlynew, $rank, $rankup);
			$this->view->offers				= Application_Service_Item::getInstance()->getOfferListForItem($this->view->collection, $start, $limit, $sort, $dir, $query, $onlynew, $rank, $rankup);
    }
    
    public function indexAction() {
			// for GET, the preDispatcher returns everything we need
			
			if ($this->getRequest()->getMethod() === 'GET') {
				foreach($this->view->offers as $existingOffer) {
					if ($existingOffer['shown'] > 1) continue;
					Application_Service_ItemOffer::getInstance()->saveItemOffer(
						$existingOffer['itemid'],
						$existingOffer['id'],
						$existingOffer['confidence'],
						time(),
						$existingOffer['rank']);
				}
				
				$this->view->format = $this->_helper->contextSwitch()->getCurrentContext();
				switch ($this->view->format) {
					case 'rss':
					case 'atom':
						echo $this->_helper->viewRenderer->render('index.feed.phtml');
						exit;
						return;
					default:
				}
			}
			
			// Offers are not really restful, both PUT and POST here handle batched status updates for existing offers
			if ($this->getRequest()->getMethod() === 'POST') {
		 		return $this->_forward('update');
		 	}
		 	if ($this->getRequest()->getMethod() === 'PUT') {
		 		return $this->_forward('update');
		 	}
		}
		
		public function updateAction() {
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			$postedOffer = $data['offers'];
			
			foreach($this->view->offers as $existingOffer) {
				if ($postedOffer['id'] == $existingOffer['id']) {
					if (isset($postedOffer['rank'])  && ($postedOffer['rank'] != $existingOffer['rank'])) {
						Application_Service_ItemOffer::getInstance()->saveItemOffer(
							$existingOffer['itemid'],
							$existingOffer['id'],
							$existingOffer['confidence'],
							$existingOffer['shown'],
							isset($postedOffer['rank']) ? $postedOffer['rank'] : $existingOffer['rank']);
					}
					
					$this->view->success 	= true;
					$this->view->offers = Application_Service_Item::getInstance()->getOfferListForItem(
   		Application_Service_Item::getInstance()->getItem($existingOffer['itemid']),
   		0, 999, '', '', $existingOffer['name']);
   				return;
				}
			}
		   
		  $this->view->success 	= false;
     	$this->view->offers = array();
		}
}
?>