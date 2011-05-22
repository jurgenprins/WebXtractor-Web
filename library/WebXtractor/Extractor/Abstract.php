<?
	abstract class WebXtractor_Extractor_Abstract {
 		private $wxCollection									= null;
		
		private $strOfferTitleFilterRegEx 		= null;
		
		function __construct(WebXtractor_Extractor_Receiver_Interface &$wxCollection) {
			$this->wxCollection = $wxCollection;
		}
	
		abstract function process(WebXtractor_Net_Url &$wxUrl, $strHtml);  // returns $res
 		abstract function getNextLinks($res);
 	
 		function getOfferTitleFilter() { return $this->strOfferTitleFilterRegEx; }
 		function setOfferTitleFilter($strOfferTitleFilterRegEx) {
 			$this->strOfferTitleFilterRegEx = $strOfferTitleFilterRegEx;
 		}
 		
 		function processOffer(WebXtractor_Extractor_Object &$wxOffer) {
 			if ($this->strOfferTitleFilterRegEx &&
 			    !preg_match('/' . $this->strOfferTitleFilterRegEx . '/i', $wxOffer->getTitle())) {
 			  return null;
 			}
 			
 			return $this->wxCollection->onOffer($wxOffer);
 		}
 		
 		function processMeta(WebXtractor_Extractor_Meta &$wxMeta) {
 			return $this->wxCollection->onMeta($wxMeta);
 		}
 		
 		function onOffersProcessed(&$wxUrl, $arrWxOffers) {
			print "<b>" . $wxUrl->getUrl() . "</b><br>\n";
	 		foreach($arrWxOffers as $wxOffer) {
				if ($wxOffer->getLink()) 	print "<a href=\"" . $wxOffer->getLink() . "\">";
				if ($wxOffer->getImage())	{
					print "<img src=\"" . $wxOffer->getImage() . "\" width=\"200\" ";
					if ($wxOffer->getTitle()) print "alt=\"" . $wxOffer->getTitle() . "\"";
					print "/>";
				} else if ($wxOffer->getTitle()) {
					print $wxOffer->getTitle();
				} else {
					print $wxOffer->getLink();
				}
				if ($wxOffer->getLink()) 	print "</a>";
				if (!$wxOffer->getImage()) print "<br>\n";
				flush();
			}
		}
 	}
?>  