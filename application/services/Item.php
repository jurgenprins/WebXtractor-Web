<?php

class Application_Service_Item extends Application_Service_Base
{
		protected $itemDao 				= null;
		
    const			GET_INCLUDE_NONE						= 0;
    const 		GET_INCLUDE_ATTRS						= 1;
    const 		GET_INCLUDE_DATASOURCES			= 2;
    const 		GET_INCLUDE_OFFERS					= 4;
    const			GET_INCLUDE_ITEMDATASOURCES	= 8;
    const			GET_INCLUDE_ITEMOFFERS			= 16;
    const			GET_INCLUDE_CHILDREN				= 32;
    
    protected function __construct() {
    	$this->itemDao 	= new Application_Model_Mapper_Item();
    }
    
    public static function getInstance() {
        return parent::getInstance(get_class());
    }
    
    public function setIncludes(&$item, $optIncludes, $start = 0, $limit = 999) {
    	if ($optIncludes & self::GET_INCLUDE_ATTRS) {
    		$item->setItemAttributes($this->itemDao->getItemAttributes($item));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_DATASOURCES) {
    		$item->setDatasources($this->itemDao->getDatasources($item));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_ITEMDATASOURCES) {
    		$item->setItemDatasources($this->itemDao->getItemDatasources($item));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_OFFERS) {
    		$item->setOffers($this->itemDao->getOffers($item, $start, $limit));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_ITEMOFFERS) {
    		$item->setItemOffers($this->itemDao->getItemOffers($item));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_CHILDREN) {
    		$item->setChildren($this->itemDao->getChildren($item));
    	}
    	
    	return $item;
    }
    
   	public function createItem() {
    	return $this->itemDao->getModel();
    }
  
    public function getItem($mixedItem, $optIncludes = self::GET_INCLUDE_NONE) {
    	if ($mixedItem instanceof Application_Model_Item) {
    		$item = $mixedItem;
    	} else if (is_numeric($mixedItem)) {
    		$item = $this->itemDao->get($mixedItem);
    	} else {
    		throw new Exception ('Not a valid item identifier');
    	}
    	
    	return $this->setIncludes($item, $optIncludes);
		}
		
		public function findItemByName($name) {
			$res = $this->itemDao->find(array('name' => $name));
			if (count($res) <= 0) {
				return null;
			}
			return $res[0];
		}
		
		public function getDatasourcesForItem(&$item) {
			$this->setIncludes($item, self::GET_INCLUDE_DATASOURCES);
			
			$datasourceSvc = Application_Service_Datasource::getInstance();
			
			$datasources = array();
			foreach($item->datasources as $i => $datasource) {
				$datasources[] = $datasourceSvc->setIncludes($datasource,
					Application_Service_Datasource::GET_INCLUDE_ATTRS);
						
			}
			return $datasources;
		}
		
		public function getOfferCountForItem(&$item, $query = '', $onlynew = 0, $rank = -1, $rankup = 0) {
			return $this->itemDao->getOfferCount($item, $query, $onlynew, $rank, $rankup);
		}
		
		public function getOfferListForItem(&$item, $start = 0, $limit = 999, $sort = 'updated', $dir = 'desc', $query = '', $onlynew = 0, $rank = -1, $rankup = 0) {
			return $this->itemDao->getOfferList($item, $start, $limit, $sort, $dir, $query, $onlynew, $rank, $rankup);
		}
		
		public function getItemTreeForUser($mixedUser, $optIncludes = self::GET_INCLUDE_NONE) {
			if (!function_exists('getItemChildren')) {
				function getItemChildren($idParent, &$resItems, &$itemSvc, $optIncludes) {
					$aChildren = array();
					foreach($resItems as $item) {
		    		if ($item->getIdparent() == $idParent) {
		    			$aChild = array(
		    				'obj' 			=> $itemSvc->getItem($item, $optIncludes),
		    				'children'	=> getItemChildren($item->getId(), $resItems, $itemSvc, $optIncludes));
		    			
		    			$aChildren[] = $aChild;
		    		}
					}
					return $aChildren;
	    	}
      }
      
			if ($mixedUser instanceof Application_Model_User) {
    		$user = $mixedUser;
    	} else if (is_numeric($mixedUser)) {
    		if (is_null($this->userDao)) $this->userDao = new Application_Model_Mapper_User();
    		$user = $this->userDao->get($mixedUser);
    	} else {
    		throw new Exception ('Not a valid user identifier');
    	}
    	
    	$resItems = $this->itemDao->findByUser($user);
    	return getItemChildren(0, $resItems, $this, $optIncludes);
		}
		
		public function saveItem(Application_Model_Item &$item) {
    	return $this->itemDao->save($item);
		}
		
		public function removeItem(Application_Model_Item &$item) {
			return $this->itemDao->delete($item);
		}
}
?>