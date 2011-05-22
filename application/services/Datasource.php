<?php

class Application_Service_Datasource extends Application_Service_Base
{
		protected $datasourceDao = null;
		
		const			DEFAULT_EXTRACTOR_ID				= 1; // html extractor
		
		const			GET_INCLUDE_NONE						= 0;
    const 		GET_INCLUDE_ATTRS						= 1;
    const 		GET_INCLUDE_EXTRACTOR				= 2;
    const 		GET_INCLUDE_ITEMS						= 4;
    const			GET_INCLUDE_ITEMDATASOURCES	= 8;
    
    protected function __construct() {
    	$this->datasourceDao = new Application_Model_Mapper_Datasource();
    }
    
    public static function getInstance()
    {
        return parent::getInstance(get_class());
    }
    
    public function setIncludes(&$datasource, $optIncludes) {
    	if ($optIncludes & self::GET_INCLUDE_ATTRS) {
    		$datasource->setDatasourceAttributes($this->datasourceDao->getDatasourceAttributes($datasource));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_EXTRACTOR) {
    		$extractorDao = new Application_Model_Mapper_Extractor();
    		
    		$extractor = $extractorDao->get($datasource->getIdextractor());
    		$extractor->setExtractorAttributes($extractorDao->getExtractorAttributes($extractor));
    		
    		$datasource->setExtractor($extractor);
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_ITEMS) {
    		$datasource->setItems(
    			$this->datasourceDao->getItems($datasource));
    	}
    	
    	if ($optIncludes & self::GET_INCLUDE_ITEMDATASOURCES) {
    		$datasource->setItemDatasources($this->datasourceDao->getItemDatasources($datasource));
    	}
    	
    	return $datasource;
    }
    
    public function createDatasource() {
    	return $this->datasourceDao->getModel('', array(
    		'idextractor' => self::DEFAULT_EXTRACTOR_ID,
    		'updated'			=> 0));
    }
    
    public function getDatasource($mixedDatasource, $optIncludes = self::GET_INCLUDE_NONE) {
    	if ($mixedDatasource instanceof Application_Model_Datasource) {
    		$datasource = $mixedDatasource;
    	} else if (is_numeric($mixedDatasource)) {
    		$datasource = $this->datasourceDao->get($mixedDatasource);
    	} else {
    		throw new Exception ('Not a valid datasource identifier');
    	}
     	
     	return $this->setIncludes($datasource, $optIncludes);
		}
			
		public function saveDatasourceAttribute(
			Application_Model_Datasource &$datasource, 
			$name, 
			$val, 
			$type = Application_Model_BaseAttribute::TYPE_STRING) 
		{
			$datasourceAttribute = new Application_Model_DatasourceAttribute($name, $val, $type);
			$datasourceAttribute->setIddatasource($datasource->getId());
			
			$datasourceAttributeDao = new Application_Model_Mapper_DatasourceAttribute();
			$datasourceAttributeDao->save($datasourceAttribute);
			
			$datasourceAttributes = $datasource->getDatasourceAttributes();
			if (!is_array($datasourceAttributes)) $datasourceAttributes = array();
			$exists = false;
			foreach($datasourceAttributes as $idx => $existingDatasourceAttribute) {
				if ($existingDatasourceAttribute->getName() == $datasourceAttribute->getName()) {
					$datasourceAttributes[$idx] = $datasourceAttribute;
					$exists = true;
				}
			}
			if (!$exists) {
				$datasourceAttributes[] = $datasourceAttribute;
			}
			$datasource->setDatasourceAttributes($datasourceAttributes);
		}
		
		public function getDatasources() {
			return $this->datasourceDao->find(array());
		}
		
    public function saveDatasource(Application_Model_Datasource &$datasource) {
    	return $this->datasourceDao->save($datasource);
		}
}
?>