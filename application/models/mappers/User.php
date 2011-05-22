<?php

class Application_Model_Mapper_User extends Application_Model_Mapper_Base
{
	public function __construct() {
		parent::__construct('User');
	}
	
	public function getItems(Application_Model_User &$user) {
		if (is_null(
			$resUser = $this->get(
				$user->getId(), 
				array('item')))) {
			return null;
		}
		return $resUser->getItems();
	}
	
  public function save(Application_Model_User &$user) {
  	
		if (!$user->getId()) {
			$where = $this->getDbTable()->getAdapter()->quoteInto('username = ?', $user->getUsername());
	   	$resultSet = $this->getDbTable()->fetchAll($where);
	   	foreach ($resultSet as $row) {
	   		$user->setId($row->id);
	   		break;
	  	}
		} 	
		return parent::save($user);
	}
}
?>