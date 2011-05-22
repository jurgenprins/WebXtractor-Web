<?php

class Application_Model_Mapper_Offer extends Application_Model_Mapper_Base
{
	public function __construct() {
		parent::__construct('Offer');
	}
	
	public function save(Application_Model_Offer &$offer) {
		if (!$offer->getId()) {
			$where = $this->getDbTable()->getAdapter()->quoteInto('url = ?', $offer->getUrl());
	   	$resultSet = $this->getDbTable()->fetchAll($where);
	   	foreach ($resultSet as $row) {
	   		$offer->setId($row->id);
	   		break;
	  	}
		}    		
      	
		return parent::save($offer);
	}
}
?>