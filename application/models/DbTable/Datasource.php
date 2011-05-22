<?php

class Application_Model_DbTable_Datasource extends Zend_Db_Table_Abstract
{
    protected $_name = 'datasource';

		protected $_dependentTables = array(
				'Application_Model_DbTable_ItemDatasource',
				'Application_Model_DbTable_DatasourceAttribute');
				
		protected $_referenceMap    = array(
        'Extractor' => array(
					'columns'           => 'idextractor',
					'refTableClass'     => 'Application_Model_DbTable_Extractor',
					'refColumns'        => 'id'
				));
}
?>