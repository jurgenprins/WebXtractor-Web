<?php

class Application_Model_DbTable_ItemOffer extends Zend_Db_Table_Abstract
{
    protected $_name = 'item_offer';
    
		protected $_referenceMap    = array(
				'Item' => array(
					'columns'           => 'iditem',
					'refTableClass'     => 'Application_Model_DbTable_Item',
					'refColumns'        => 'id'
				),
				'Offer' => array(
					'columns'           => 'idoffer',
					'refTableClass'     => 'Application_Model_DbTable_Offer',
					'refColumns'        => 'id'
				)
		);
}
?>