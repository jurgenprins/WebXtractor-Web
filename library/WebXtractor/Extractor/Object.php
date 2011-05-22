<?
	class WebXtractor_Extractor_Object {
		private $wxSrcUrl	= null;
 		private $strLink	= null;
 		private $strTitle = null;
 		private $strImage = null;
 		
 		function __construct(WebXtractor_Net_Url $wxSrcUrl, $strLink, $strTitle = '', $strImage = '') {
 			$this->wxSrcUrl		= $wxSrcUrl;
 			$this->strLink		= $strLink;
 			$this->strTitle		= $strTitle;
 			$this->strImage		= $strImage;
 		}
 			
 		function getSourceUrl()	{ return $this->wxSrcUrl; }
 		function getLink()			{ return $this->strLink; }
 		function getTitle()			{ return $this->strTitle; }
 		function getImage()			{ return $this->strImage; }
 	}
?>