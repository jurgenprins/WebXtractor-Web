<?php

class Application_Form_Datasource extends Application_Form_Base
{
    public function init()
    {
    	parent::init();
    	
    	$this->addElement('text', 'url', array_merge(
    		$this->getUrlConfig(), 
    		array(
    			'required'	=> true,
    			'label' => 'URL'
    	)));
			
			$this->addElement('text', 'name', array_merge(
				$this->getTextConfig(), 
				array(
					'label' => 'Name'
			)));
			
			$extractorSvc = Application_Service_Extractor::getInstance();
			$extractors = $extractorSvc->findExtractors();
			$opts = array();
			foreach($extractors as $extractor) {
				$opts[$extractor->getId()] = $extractor->getName();
			}
			
			$this->addElement('select', 'idextractor',
				array(
					'label' 					=> 'Extractor',
					'multioptions'   	=> $opts,
					'decorators'			=> $this->getElementDecorators()
			));
			
			$this->addElement('submit', 'save', array_merge(
				$this->getButConfig(), 
				array(
					'label' => 'Save'
			)));
	}
}
?>