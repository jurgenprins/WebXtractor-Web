<?php

class Application_Model_DbTable_ItemAttribute extends Zend_Db_Table_Abstract
{
    protected $_name = 'itemattribute';

		protected $_referenceMap    = array(
        'Item' => array(
					'columns'           => 'iditem',
					'refTableClass'     => 'Application_Model_DbTable_Item',
					'refColumns'        => 'id'
				)
		);
}
?>