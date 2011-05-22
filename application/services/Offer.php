<?php

class Application_Service_Offer extends Application_Service_Base
{
		protected $offerDao 		= null;
		
		protected function __construct() {
    	$this->offerDao 		= new Application_Model_Mapper_Offer();
    }
    
    public static function getInstance() {
        return parent::getInstance(get_class());
    }
    
    public function setIncludes(&$offer, $optIncludes) {
    	return $offer;
    }
    
    public function getOffer($mixedOffer, $optIncludes = self::GET_INCLUDE_NONE) {
    	if ($mixedOffer instanceof Application_Model_Offer) {
    		$offer = $mixedOffer;
    	} else if (is_numeric($mixedDatasource)) {
    		$offer = $this->offerDao->get($mixedOffer);
    	} else {
    		throw new Exception ('Not a valid offer identifier');
    	}
     	
     	return $this->setIncludes($datasource, $optIncludes);
		}
		
		public function saveOffer(Application_Model_Offer &$offer) {
			$this->offerDao->save($offer);
		}
		
		public function removeOffer(Application_Model_Offer &$offer) {
			return $this->offerDao->delete($offer);
		}
}
?>