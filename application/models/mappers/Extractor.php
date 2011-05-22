<?php

class Application_Model_Mapper_Extractor extends Application_Model_Mapper_Base
{
	public function __construct() {
		parent::__construct('Extractor');
	}
	
	public function getExtractorAttributes(Application_Model_Extractor &$extractor) {
		if (is_null(
			$resExtractor = $this->get(
				$extractor->getId(), 
				array('extractorAttribute')))) {
			return null;
		}
		return $resExtractor->getExtractorAttributes();
	}

}
?>