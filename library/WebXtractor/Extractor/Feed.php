<?
	date_default_timezone_set('Europe/Paris');
	
	class WebXtractor_Extractor_Feed extends WebXtractor_Extractor_Abstract {
		function getOfferFromFeedEntry(&$wxUrl, $entry) {
			return new WebXtractor_Extractor_Object($wxUrl, $entry->getLink(), $entry->getTitle(), '');
 		}
 		
		function process(WebXtractor_Net_Url &$wxUrl, $strData) {
			$arrOffersIndexed = array();
			
			$feed = Zend_Feed_Reader::importString($strData); 
			
			if ($strTitle = $feed->getTitle()) {
				$this->processMeta(new WebXtractor_Extractor_Meta($wxUrl, $strTitle));
			}
			
			foreach($feed as $entry) {
				$wxOffer = $this->getOfferFromFeedEntry($wxUrl, $entry);
				if (is_null($wxOffer)) {
					continue;
				}
				
				$arrOffersIndexed[] = $this->processOffer($wxOffer);
			}
			
			Zend_Registry::get('log')->debug("FEED EXTRACTOR: " . count($arrOffersIndexed) . ' OFFERS PROCESSED');
			
			$this->onOffersProcessed($wxUrl, array_filter($arrOffersIndexed));
			
			return $feed;
		}
		
		function onOffersProcessed(&$wxUrl, $arrWxOffers) { }
		
		function getNextLinks($feed) {
			return array();
		}
	}
?>