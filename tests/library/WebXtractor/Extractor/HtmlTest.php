<?
	class WebXtractor_Extractor_Html_Test extends PHPUnit_Framework_TestCase implements WebXtractor_Extractor_Receiver_Interface {

		protected $extractor 			= null;
		protected $offerCount			= 0;
		
		public function setUp() {
			$this->extractor	= new WebXtractor_Extractor_Html($this);
		}

	  public function testImageExtraction01() {
	    $this->offerCount	 = 0;

			$this->extractor->setAllowImageBlockOffers(true);
	    $this->extractor->setMinImageBlockOffers(8);
	    $this->extractor->setAllowLinkBlockOffers(false);
	    	    
	    $wxUrl		= new WebXtractor_Net_Url('file:///' . dirname(__FILE__) . '/../../../data/test-01.html');
	    $strHtml	= file_get_contents($wxUrl->getUrl());
	    
	    $wxHtmlParser = $this->extractor->process($wxUrl, $strHtml);
			$this->assertEquals(175, $this->offerCount);
			
			$arrLinksToFollow = $this->extractor->getNextLinks($wxHtmlParser);
			$this->assertEquals(5, count($arrLinksToFollow));
	  }

	  public function testImageExtraction02() {
	    $this->offerCount	 = 0;
	    
	    $this->extractor->setAllowImageBlockOffers(true);
	    $this->extractor->setMinImageBlockOffers(8);
	    $this->extractor->setAllowLinkBlockOffers(false);
	    
	    $wxUrl		= new WebXtractor_Net_Url('file:///' . dirname(__FILE__) . '/../../../data/test-02.html');
	    $strHtml	= file_get_contents($wxUrl->getUrl());
	    
	    $wxHtmlParser = $this->extractor->process($wxUrl, $strHtml);
			$this->assertEquals(277, $this->offerCount);
			
			$arrLinksToFollow = $this->extractor->getNextLinks($wxHtmlParser);
			$this->assertEquals(5, count($arrLinksToFollow));
	  }

		public function onOffer(WebXtractor_Extractor_Object &$wxOffer) {
			$this->offerCount++;
		}
	}
?>