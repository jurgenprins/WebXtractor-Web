<?php
	class Application_Plugin_HeaderAccept extends Zend_Controller_Plugin_Abstract { 
		public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request ) { 
			$header = $request->getHeader('Accept'); 
			switch (true) { 
				case (strstr($header , 'application/json' )): 
					$request->setParam('format', 'json' ); 
					break ; 
				case (strstr($header , 'application/xml' ) && (!strstr($header , 'html' ))): 
					$request->setParam('format', 'xml' ); 
					break ; 
			} 
			
			$vary = array('Accept');
			$response = $this->getResponse();
			foreach ($response->getHeaders() as $header) {
				if (strtolower($header['name']) != 'vary') continue;
				$vary[] = $header['value'];
				$response->clearHeader($header['name']);
			}
			$response->setHeader('Vary', implode(', ', $vary));
		} 
	} 
?>