<?php

class DatasourceController extends Zend_Controller_Action
{
		public function init()
    {
        $this->_helper->contextSwitch()
             ->addActionContext('index', 'json')
             ->addActionContext('post', 'json')
             ->addActionContext('put', 'json')
             ->addActionContext('delete', 'json')
             ->addActionContext('run', 'json')
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
			$sort		= $this->_getParam('sort') 	? strtolower($this->_getParam('sort'))	: 'id';
			$dir		= $this->_getParam('dir') 	? strtolower($this->_getParam('dir')) 	: 'asc';
	
			$this->view->datasources = Application_Service_Item::getInstance()->getDatasourcesForItem($this->view->collection);
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

       	$datasourceSvc = Application_Service_Datasource::getInstance();
     	 	$datasource = $datasourceSvc->getDatasource($this->getDatasourceFromRequest(), 
       		Application_Service_Datasource::GET_INCLUDE_ATTRS |
       		Application_Service_Datasource::GET_INCLUDE_EXTRACTOR);
        $extractorAttributes = $datasource->getExtractor()->getExtractorAttributes();
       	
       	$this->view->datasource = $datasource;
       	$this->view->datasource_form = $this->getDatasourceForm($datasource->getOptions());
       	$this->view->datasource_extractor_form = $this->getDatasourceExtractorForm(
       		$datasource->getDatasourceAttributes(), 
       		$extractorAttributes);
       	$this->view->datasource_indexer_config_form = $this->getDatasourceIndexerConfigForm(
       		$datasource->getDatasourceAttributes());
				$this->view->datasource_indexer_run_form = $this->getDatasourceIndexerRunForm();      		
       	$this->view->active = 'datasource_form';
       	
       	if ($this->getRequest()->isPost()) {
       		switch($this->getRequest()->getPost('class')) {
       			case 'Datasource':
			       	if ($this->view->datasource_form->isValid($_POST)) {
		       			$datasource->setOptions($this->view->datasource_form->getValues());
		       			$datasourceSvc->saveDatasource($datasource);
			       		
			       		$this->_redirect('/' . $this->view->navigation()->findOneById('user_collections')->getUri() . $this->view->collection->getName() . '/datasources/' . $datasource->getId());
			       	} else {
			       		$this->view->active = 'datasource_form';
			       	}
			       	break;
			      case 'Base':
			      	if ($this->view->datasource_extractor_form->isValid($_POST)) {
			      		foreach($this->view->datasource_extractor_form->getValues() as $name => $val) {
			      			$type = Application_Model_BaseAttribute::TYPE_STRING;
			      			foreach($extractorAttributes as $extractorAttribute) {
			      				if ($extractorAttribute->getName() == $name) {
			      					$datasourceSvc->saveDatasourceAttribute($datasource, $name, $val, $extractorAttribute->getType());
			      					break;
			      				}
			      			}
			      		}
			      	} 
			      	$this->view->active = 'datasource_extractor_form';
			      	break;
			     	case 'DatasourceIndexerConfig':
			      	if ($this->view->datasource_indexer_config_form->isValid($_POST)) {
			      		foreach($this->view->datasource_indexer_config_form->getValues() as $name => $val) {
			      			switch ($name) {
			      				case 'MaxLinksToFollow':
			      				case 'RunEvery':
			      					$datasourceSvc->saveDatasourceAttribute($datasource, $name, $val, Application_Model_BaseAttribute::TYPE_INT);
			      					break;
			      				default:
			      			}
				      	}
				      }
				      $this->view->active = 'datasource_indexer_config_form';
			      	break;
			      case 'DatasourceIndexerRun':
			      	$datasourceSvc->saveDatasourceAttribute($datasource, 'RunStatus', 1, Application_Model_BaseAttribute::TYPE_INT);
			      	$this->view->active = 'datasource_indexer_run_form';
			      	break;
			      default:
			  	}
       	}
		}
		
		public function postAction() {
			$datasource = Application_Service_Datasource::getInstance()->createDatasource();
			$datasourceForm = $this->getDatasourceForm($datasource->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
		
			if (!$datasourceForm->isValid($data['datasources'])) {
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->datasource = $datasource;
				return;
			}
			
			$datasource->setOptions($data['datasources']);
			
			Application_Service_Datasource::getInstance()->saveDatasource($datasource);
			
			Application_Service_ItemDatasource::getInstance()->addItemDatasource($this->view->collection, $datasource);
	
			$this->getResponse()->setHttpResponseCode(201);
        
     	$this->view->success = true;
			$this->view->datasource = $datasource;
			
			//$this->view->location = $this->getRequest()->getBaseUrl() . $this->view->navigation()->findOneById('user_collections')->getUri() . $this->_getParam('collectionname') . '/datasources/' . $datasource->getId();
    }
    
		public function putAction() {
			$datasource = Application_Service_Datasource::getInstance()->getDatasource($this->getDatasourceFromRequest());
			$datasourceForm = $this->getDatasourceForm($datasource->getOptions());
			
			$data = Zend_Json::decode($this->getRequest()->getRawBody());
			
			if (!$datasourceForm->isValid($data['datasources'])) {
				$this->getResponse()->setHttpResponseCode(400);
				$this->view->success = false;
				$this->view->datasource = $datasource;
				return;
			}
			
			$datasource->setOptions($data['datasources']);
		
		  Application_Service_Datasource::getInstance()->saveDatasource($datasource);
		  
     	$this->view->success = true;
			$this->view->datasource = $datasource;
		}
			
		public function deleteAction() {
			$datasource = Application_Service_Datasource::getInstance()->getDatasource($this->getDatasourceFromRequest());
			
			Application_Service_ItemDatasource::getInstance()->removeItemDatasource($this->view->collection, $datasource);
			
			$this->getResponse()->setHttpResponseCode(204);
			
			$this->view->success = true;
    }

		public function runAction() {
			$indexerSvc				= new Application_Service_Indexer($this->getDatasourceFromRequest());
			$indexerSvc->run();
   	}
   	
   	protected function getDatasourceForm(array $data) {
			$form = new Application_Form_Datasource();
     	$form->setDefaults($data);
			return $form;
		}

		protected function getDatasourceExtractorForm(array $datasource_data = null, array $extractor_data) {
			$form = new Application_Form_Base();
			$data = array();
			foreach($extractor_data as $extractorAttribute) {
				switch($extractorAttribute->getType()) {
					case Application_Model_BaseAttribute::TYPE_BOOL:
						$form->addElement(
							'checkbox', 
							$extractorAttribute->getName(), array_merge(
								$form->getTextConfig(),
			        	array(
			            'label'      => $extractorAttribute->getName(),
		        )));
						break;
					case Application_Model_BaseAttribute::TYPE_INT:
						$form->addElement(
							'text', 
							$extractorAttribute->getName(), array_merge(
								$form->getTextConfig(),
			        	array(
			        		'style'				=> 'width:50px',
			        		'validators' => array(
			                'Int',
			                array('StringLength', false, array(1, 3)),
			            ),
			            'label'      => $extractorAttribute->getName(),
		        )));
						break;
					case Application_Model_BaseAttribute::TYPE_STRING:
					default:
						$form->addElement(
							'text', 
							$extractorAttribute->getName(), array_merge(
								$form->getTextConfig(),
			        	array(
			        		'style'				=> 'width:200px',
			            'label'      => $extractorAttribute->getName(),
		        )));
		    }
				$data[$extractorAttribute->getName()] = $extractorAttribute->getVal();
			}
			foreach($datasource_data as $datasourceAttribute) {
				if (isset($data[$datasourceAttribute->getName()])) {
					$data[$datasourceAttribute->getName()] = $datasourceAttribute->getVal();
				}
			}
			
			$form->addElement('submit', 'save', array_merge(
				$form->getButConfig(), 
				array(
					'label' => 'Save'
			)));
     	$form->setDefaults($data);
			return $form;
		}			
	
		protected function getDatasourceIndexerConfigForm(array $datasource_data = null) {
			$form = new Application_Form_DatasourceIndexerConfig();
			$data = array();
			foreach($datasource_data as $datasourceAttribute) {
				switch($datasourceAttribute->getName()) {
					case 'MaxLinksToFollow':
					case 'RunEvery':
						$data[$datasourceAttribute->getName()] = $datasourceAttribute->getVal();
						break;
					default:
				}
			}
			$form->setDefaults($data);
			return $form;
		}
		
		protected function getDatasourceIndexerRunForm() {
			$form = new Application_Form_DatasourceIndexerRun();
			return $form;
		}
		
	 	public function getDatasourceFromRequest() {
     	foreach($this->view->datasources as $datasource) {
     		if ($datasource->getId() == $this->_getParam('datasourceid')) {
     			return $datasource;
     		}
     	}
     	
     	throw new Exception('Datasource ' . $this->_getParam('datasourceid') . ' does not exist', 404) ;
   	}
}