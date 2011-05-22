<?php

class Application_Model_Mapper_Item extends Application_Model_Mapper_Base
{
	public function __construct() {
		parent::__construct('Item');
	}
	
	public function getItemAttributes(Application_Model_Item &$item) {
		if (is_null(
			$resItem = $this->get(
				$item->getId(), 
				array('itemAttribute')))) {
			return null;
		} 
		return $resItem->getItemAttributes();
	}
	
	public function getDatasources(Application_Model_Item &$item) {
		if (is_null(
			$resItem = $this->get(
				$item->getId(), 
				array(array('datasource' => 'itemDatasource'))))) {
			return null;
		}
		return $resItem->getDatasources();
	}
	
	public function getItemDatasources(Application_Model_Item &$item) {
		if (is_null(
			$resItem = $this->get
				($item->getId(),
				array('itemDatasource')))) {
			return null;
		}
		return $resItem->getItemDatasources();
	}	
	
	public function getItemOffers(Application_Model_Item &$item) {
		if (is_null(
			$resItem = $this->get
				($item->getId(),
				array('itemOffer')))) {
			return null;
		}
		return $resItem->getItemOffers();
	}	
	
	public function getOffers(Application_Model_Item &$item, $start = 0, $limit = 999) {
		if (is_null(
			$resItem = $this->get(
				$item->getId(), 
				array(array('offer' => 'itemOffer')),
				null,
				$start, $limit))) {
			return null;
		}
		return $resItem->getOffers();
	}
	
	public function getUser(Application_Model_Item &$item) {
		if (is_null(
			$resItem = $this->get(
				$item->getId(),
				null,
				array('user')))) {
			return null;
		}
		return $resItem->getUser();
	}
	
	public function getParent(Application_Model_Item &$item) {
		if (is_null(
			$resItem = $this->get(
				$item->getId(),
				null,
				array(array('item' => 'parent'))))) {
			return null;
		}
		return $resItem->getParent();
	}
	
	public function getChildren(Application_Model_Item &$item) {
		return $this->find(
				array('idparent' => $item->getId()));
	}
	
	public function findByUser(Application_Model_User &$user) {
		return $this->find(
			array('iduser' => $user->getId()));
	}
	
	public function getOfferList(Application_Model_Item &$item, $start = 0, $limit = 999, $sort = 'updated', $dir = 'desc', $query = '', $onlynew = 0, $rank = -1, $rankup = 0) {
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false);
		$select->from(array('i' => 'item'), array('id as itemid', 'name as itemname'));
		
		if (is_array($children = $this->getChildren($item)) &&
		    count($children)) {
			$select->where('i.idparent = ?', $item->getId());
		} else {
			$select->where('i.id = ?', $item->getId());
		}
		if ($query) {
			$select->where('o.name like ?', '%' . $query . '%');
		}
		if ($onlynew) {
			$select->where('io.shown = 0 OR io.shown > ' . (time() - 600));
		}
		if ($rank >= 0) {
			$select->where('io.rank ' . ($rankup ? '>' : '') . '= ?', 0 + $rank);
		}
		
		$select->join(array('io' => 'item_offer'), 'i.id = io.iditem', array('created', 'updated', 'confidence', 'shown', 'rank'));
		$select->join(array('o' => 'offer'), 'io.idoffer = o.id', array('id', 'name', 'url', 'img'));
		$select->join(array('d' => 'datasource'), 'o.iddatasource = d.id', array('name as datasource'));
		
		switch($sort) {
			case 'name': 		$sort = 'o.name'; 			break;
			case 'rank':
			case 'updated':	$sort = 'io.' . $sort; 	break;
			default: 				$sort = '';
		}
		if ($sort) {
			$select->order($sort . ' ' . (($dir == 'desc') ? 'desc' : 'asc'));
    }
	  $select->limit($limit, $start);
	  
	  $offers = array();
	  $res = $this->getDbTable()->fetchAll($select);
	  foreach ($res as $row) {
			$offers[] = $row->toArray();
		}
		return $offers;
	}
	
	public function getOfferCount(Application_Model_Item &$item, $query = '', $onlynew = 0, $rank = -1, $rankup = 0) {
		$select = $this->getDbTable()->select();
		$select->setIntegrityCheck(false);
		$select->from(array('i' => 'item'), array('name as itemname'));
		
		if (is_array($children = $this->getChildren($item)) &&
		    count($children)) {
			$select->where('i.idparent = ?', $item->getId());
		} else {
			$select->where('i.id = ?', $item->getId());
		}
		if ($query) {
			$select->where('o.name like ?', '%' . $query . '%');
		}
		if ($onlynew) {
			$select->where('io.shown = 0 OR io.shown > ' . (time() - 600));
		}
		if ($rank >= 0) {
			$select->where('io.rank ' . ($rankup ? '>' : '') . '= ?', 0 + $rank);
		}
		
		$select->join(array('io' => 'item_offer'), 'i.id = io.iditem', array('created'));
		
		return $this->getDbTable()->fetchAll($select)->count();
	}

}
?>