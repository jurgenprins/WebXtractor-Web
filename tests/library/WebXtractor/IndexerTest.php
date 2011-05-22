<?
	class WebXtractor_Indexer_Test extends PHPUnit_Framework_TestCase implements WebXtractor_Extractor_Receiver_Interface {

		protected $indexer 				= null;
		protected $offerCount			= 0;
		
		public function setUp() {
			$this->indexer 		= new WebXtractor_Indexer();
		}

	  public function testHtmlImageExtraction() {
	    $this->offerCount	 = 0;
	    
	    $url 				= new WebXtractor_Net_Url('file:///' . dirname(__FILE__) . '/../../data/britney-spears-01.html');
	    
	    $extractor	= new WebXtractor_Extractor_Html($this);	    
	    $extractor->setAllowImageBlockOffers(true);
	    $extractor->setMinImageBlockOffers(8);
	    $extractor->setAllowLinkBlockOffers(false);
	    
	    $this->indexer->setMaxFollowPaginatedLinks(2);
	    $this->indexer->index($url, $extractor);

			$this->assertEquals(452, $this->offerCount);
	  }

		public function onOffer(WebXtractor_Extractor_Object &$wxOffer) {
			$this->offerCount++;
		}
	}
?>