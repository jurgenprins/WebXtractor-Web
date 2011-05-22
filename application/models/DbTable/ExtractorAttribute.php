<?php

class Application_Model_DbTable_ExtractorAttribute extends Zend_Db_Table_Abstract
{
    protected $_name = 'extractorattribute';

		protected $_referenceMap    = array(
        'Extractor' => array(
					'columns'           => 'idextractor',
					'refTableClass'     => 'Application_Model_DbTable_Extractor',
					'refColumns'        => 'id'
				)
		);
}
?>