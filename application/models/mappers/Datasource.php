<?php

class Application_Model_Mapper_Datasource extends Application_Model_Mapper_Base
{
	public function __construct() {
		parent::__construct('Datasource');
	}
	
	public function getDatasourceAttributes(Application_Model_Datasource &$datasource) {
		if (is_null(
			$resDatasource = $this->get(
				$datasource->getId(), 
				array('datasourceAttribute')))) {
			return null;
		}
		return $resDatasource->getDatasourceAttributes();
	}
	
	public function getItemDatasources(Application_Model_Datasource &$datasource) {
		if (is_null(
			$resDatasource = $this->get
				($datasource->getId(),
				array('itemDatasource')))) {
			return null;
		}
		return $resDatasource->getItemDatasources();
	}		
    				
	public function getExtractor(Application_Model_Datasource $datasource) {
		if (is_null(
			$resDatasource = $this->get(
				$datasource->getId(),
				null,
				array('extractor')))) {
			return null;
		}
		return $resDatasource->getExtractor();
	}
	
	public function getItems(Application_Model_Datasource &$datasource) {
		if (is_null(
			$resItem = $this->get(
				$datasource->getId(), 
				array(array('item' => 'itemDatasource'))))) {
			return null;
		}
		return $resItem->getItems();
	}
	
	public function save(Application_Model_Datasource &$datasource) {
		if (!$datasource->getId()) {
			$where = $this->getDbTable()->getAdapter()->quoteInto('url = ?', $datasource->getUrl());
	   	$resultSet = $this->getDbTable()->fetchAll($where);
	   	foreach ($resultSet as $row) {
	   		$datasource->setId($row->id);
	   		break;
	  	}
		}    		
      	
		return parent::save($datasource);
	}
}
?>