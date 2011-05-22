<?php

class Application_Service_Indexer implements WebXtractor_Extractor_Receiver_Interface
{
		protected $indexer 				= null;
		protected $datasource 		= null;
		protected $extractor 			= null;
		
		protected $offerSvc				= null;
		protected $itemOfferSvc		= null;
		
    public function __construct(Application_Model_Datasource &$datasource) {
    	$this->indexer 		= new WebXtractor_Indexer();
    	$this->indexer->setMaxFollowPaginatedLinks(1);
    	
    	$datasourceSvc 	= Application_Service_Datasource::getInstance();
    	$this->datasource	= $datasourceSvc->getDatasource($datasource, 
       		Application_Service_Datasource::GET_INCLUDE_ATTRS |
       		Application_Service_Datasource::GET_INCLUDE_EXTRACTOR |
       		Application_Service_Datasource::GET_INCLUDE_ITEMS);

			$attributes 					= $this->datasource->getDatasourceAttributes();
    	foreach($attributes as $datasourceAttribute) {
    		if ($datasourceAttribute->getName() == 'MaxLinksToFollow') {
    			$this->indexer->setMaxFollowPaginatedLinks($datasourceAttribute->getVal());
    		}
    	}       		
    	
    	$this->offerSvc 		= Application_Service_Offer::getInstance();
    	$this->itemOfferSvc	= Application_Service_ItemOffer::getInstance();
    }
    
    public function getConfiguredExtractor() {
    	if (is_null($this->datasource)) {
    		throw new Exception('datasource must be instantiated');
    	}
    	
    	if (!is_null($this->extractor)) {
    		return $this->extractor;
    	}
    	
    	switch ($this->datasource->getIdextractor()) {
    		case 1:  	$this->extractor 		= new WebXtractor_Extractor_Html($this);	break;
    		case 2:		$this->extractor 		= new WebXtractor_Extractor_Feed($this);	break;
    		default:	throw new Exception('unknown extractor');
    	}
    	
    	$attributes 					= $this->datasource->getDatasourceAttributes();
    	$extractorAttributes	= $this->datasource->getExtractor()->getExtractorAttributes();
    	
    	foreach($extractorAttributes as $extractorAttribute) {
	    	foreach($attributes as $datasourceAttribute) {
	    		if ($extractorAttribute->getName() == $datasourceAttribute->getName()) {
  	  			$method = 'set' . ucfirst($datasourceAttribute->getName());
    				$this->extractor->$method($datasourceAttribute->getVal());
    			}
    		}
    	}
    	
    	return $this->extractor;
    }
    
    public function run() {
    	if (is_null($this->datasource)) {
    		throw new Exception('datasource must be instantiated');
    	}

    	$url = new WebXtractor_Net_Url($this->datasource->getUrl());
    
    	$ex = $this->getConfiguredExtractor();
 			
 			$this->indexer->index($url, $this->getConfiguredExtractor());
 			
 			$this->datasource->setUpdated(time());
 			Application_Service_Datasource::getInstance()->saveDatasource($this->datasource);
 			
    }
    
    public function onOffer(WebXtractor_Extractor_Object &$wxOffer) {
    	if (is_null($this->datasource)) {
    		throw new Exception('datasource must be instantiated');
    	}
    	
    	$offer = new Application_Model_Offer();
			$offer->setName($wxOffer->getTitle());
			$offer->setUrl($wxOffer->getLink());
			$offer->setImg($wxOffer->getImage());
			$offer->setIddatasource($this->datasource->getId());
	
    	$items = $this->datasource->getItems();
    	if (is_array($items)) {
    		foreach($items as $item) {
    			if (!$offer->getId()) {
    				$this->offerSvc->saveOffer($offer);
    			}
    			
    			$handlingCriteria = $this->itemOfferSvc->getHandlingCriteriaForDatasource($item, $this->datasource);
    			
    			$nAdditions = $this->itemOfferSvc->addItemOffer($item, $offer, $handlingCriteria, 100, time());
    			if (0 == $nAdditions) {
    				$this->offerSvc->removeOffer($offer);
    			}
    		}
    	}
    }
    
    public function onMeta(WebXtractor_Extractor_Meta &$wxMeta) {
    	if (is_null($this->datasource)) {
    		throw new Exception('datasource must be instantiated');
    	}
    	
    	if (strcmp($wxMeta->getSourceUrl()->getUrl(), $this->datasource->getUrl())) {
    		return;
    	}
    	
    	$strTitle = substr($wxMeta->getTitle(), 0, 255);
    	if (!$strTitle) {
    		return;
    	}
    	
    	$this->datasource->setName($strTitle);
 			Application_Service_Datasource::getInstance()->saveDatasource($this->datasource);
  	}
}
?>