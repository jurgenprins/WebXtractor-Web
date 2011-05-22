<?php

class Application_Service_Extractor extends Application_Service_Base
{
		protected $extractorDao 	= null;
		
    const			GET_INCLUDE_NONE					= 0;
    const 		GET_INCLUDE_ATTRS					= 1;
    
    protected function __construct() {
    	$this->extractorDao = new Application_Model_Mapper_Extractor();
    }
    
    public static function getInstance()
    {
        return parent::getInstance(get_class());
    }
    
    public function findExtractors() {
			return $this->extractorDao->find(array());
		}
}
?>