<?php

class Application_Model_DbTable_Extractor extends Zend_Db_Table_Abstract
{

    protected $_name = 'extractor';

		protected $_dependentTables = array(
				'Application_Model_DbTable_ExtractorAttribute'); 

}
?>