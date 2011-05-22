<?php

class Application_Model_DbTable_DatasourceAttribute extends Zend_Db_Table_Abstract
{
    protected $_name = 'datasourceattribute';

		protected $_referenceMap    = array(
        'Datasource' => array(
					'columns'           => 'iddatasource',
					'refTableClass'     => 'Application_Model_DbTable_Datasource',
					'refColumns'        => 'id'
				)
		);
}
?>