<?php

class Application_Model_DbTable_Offer extends Zend_Db_Table_Abstract
{

    protected $_name = 'offer';
	
		protected $_dependentTables = array(
				'Application_Model_DbTable_ItemOffer');
	

}
?>