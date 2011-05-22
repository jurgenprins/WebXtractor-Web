<?php

class Application_Service_ItemDatasource extends Application_Service_Base
{
		protected $datasourceDao 			= null;
		protected $itemDatasourceDao	= null;
	
		protected function __construct() {
    	$this->datasourceDao			= new Application_Model_Mapper_Datasource();
    	$this->itemDatasourceDao	= new Application_Model_Mapper_ItemDatasource();
    }
    
    public static function getInstance() {
        return parent::getInstance(get_class());
    }
    
		public function addItemDatasource(
			Application_Model_Item 					&$item, 
			Application_Model_Datasource		&$datasource,
			$interval = 1, $minmatchscore = 65, $createitemonnomatch = false) 
		{
			$this->saveItemDatasource($item, $datasource, $interval, $minmatchscore, $createitemonnomatch);
		}
		
		public function saveItemDatasource(
			Application_Model_Item 					&$item,
			Application_Model_Datasource 		&$datasource,
			$interval = 1, $minmatchscore = 65, $createitemonnomatch = false)
		{
			if (!$item->getId()) {
				throw new Exception('item not known');
			}
			if (!$datasource->getId()) {
				throw new Exception('datasource not known');
			}
			
			Zend_Registry::get('log')->debug("SET DATASOURCE " . $datasource->getName() . " TO " . $item->getName());
			
			$itemDatasource = new Application_Model_ItemDatasource();
			$itemDatasource->setIditem($item->getId());
			$itemDatasource->setIddatasource($datasource->getId());
				
			$itemDatasource->setInterval($interval);
			$itemDatasource->setMinmatchscore($minmatchscore);
			$itemDatasource->setCreateitemonnomatch($createitemonnomatch ? 1 : 0);
			
			$this->itemDatasourceDao->save($itemDatasource);
			
			$itemDatasources = $item->getItemDatasources();
			if (!is_array($itemDatasources)) $itemDatasources = array();
			$exists = false;
			foreach($itemDatasources as $idx => $existingItemDatasource) {
				if ($existingItemDatasource->getId() == $itemDatasource->getId()) {
					$itemDatasources[$idx] = $itemDatasource;
					$exists = true;
				}
			}
			if (!$exists) {
				$itemDatasources[] = $itemDatasource;
			}
			$item->setItemDatasources($itemDatasources);
		}
		
		public function removeItemDatasource(
			Application_Model_Item 					&$item, 
			Application_Model_Datasource		&$datasource) {
				
			Zend_Registry::get('log')->debug("REMOVE DATASOURCE LINK " . $datasource->getName() . " TO " . $item->getName());
			
			$resItemDatasource = $this->itemDatasourceDao->find(array(
				'iditem' 				=> $item->getId(),
				'iddatasource'	=> $datasource->getId()));
				
			if (0 == count($resItemDatasource)) {
				return false;
			}
			
			Zend_Registry::get('log')->debug("FOUND DATASOURCE LINK " . $datasource->getName() . " TO " . $item->getName());
			
			$this->itemDatasourceDao->delete($resItemDatasource[0]);
			
			// delete datasource if no more items link to it anymore..
			$items = $this->datasourceDao->getItems($datasource);
			if (!is_array($items) || !count($items)) {
				$this->datasourceDao->delete($datasource);
			}
		}
}
?>