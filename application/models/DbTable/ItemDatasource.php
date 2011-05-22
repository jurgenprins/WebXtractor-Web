<?php

class Application_Model_DbTable_ItemDatasource extends Zend_Db_Table_Abstract
{
    protected $_name = 'item_datasource';
    
		protected $_referenceMap    = array(
				'Item' => array(
					'columns'           => 'iditem',
					'refTableClass'     => 'Application_Model_DbTable_Item',
					'refColumns'        => 'id'
				),
				'Datasource' => array(
					'columns'           => 'iddatasource',
					'refTableClass'     => 'Application_Model_DbTable_Datasource',
					'refColumns'        => 'id'
				)
		);
}
?>