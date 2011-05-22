<?php

class Application_Model_DbTable_Item extends Zend_Db_Table_Abstract
{
    protected $_name = 'item';

		protected $_dependentTables = array(
				'Application_Model_DbTable_Item', 
				'Application_Model_DbTable_ItemDatasource',
				'Application_Model_DbTable_ItemOffer',
				'Application_Model_DbTable_ItemAttribute');
		
		protected $_referenceMap    = array(
        'User' => array(
					'columns'           => 'iduser',
					'refTableClass'     => 'Application_Model_DbTable_User',
					'refColumns'        => 'id'
				),
				'Parent' => array(
					'columns'           => 'idparent',
					'refTableClass'     => 'Application_Model_DbTable_Item',
					'refColumns'        => 'id'
				)
		);
}
?>