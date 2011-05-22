<?
	class WebXtractor_Indexer_Map {
		private $map;
		
		function put($strKey, $val) {
			$this->map[$strKey] = $val;
		}
		
		function get($strKey) {
			return $this->map[$strKey];
		}
		
		function contains($strKey) {
			return array_key_exists($strKey, $this->map);
		}
		
		function find() {
			$a = array();
			foreach($this->map as $strKey => $val) {
				$a[] = array('url' => $strKey, 'status' => $val['status']);
			}
			return $a;
		}
		
		function size() {
			return count(array_keys($this->map));
		}
		
		function drop() {
			$this->map = array();
		}
	}
		
 	class WebXtractor_Indexer_Board {
 		const 	STATUS_FAILED 				= -1;
 		const 	STATUS_NAVAIL 				= 0;
 		const 	STATUS_QUEUED 				= 1;
 		const 	STATUS_PROCESSED 			= 2;
 		
 		private $wxMap								= null;
 		public 	$id										= null;
 		
 		function __construct() {
 			$this->wxMap	= new WebXtractor_Indexer_Map();
 			$this->id			= uniqid('');
 		}
 		
 		function setStatus($strUrl, $enumStatus) {
 			Zend_Registry::get('log')->debug('INDEX BOARD: ' . $this->id . ' PUT ' . $strUrl . ' TO ' . $enumStatus);
 			return $this->wxMap->put($strUrl, array('status' => $enumStatus));
 		}
 		
 		function getStatus($strUrl) {
 			$enumStatus = !is_null($arrRes = $this->wxMap->get($strUrl)) ? $arrRes['status'] : self::STATUS_NAVAIL;
 			Zend_Registry::get('log')->debug('INDEX BOARD: ' . $this->id . ' GET ' . $strUrl . ' AS ' . $arrRes['status']);
 			return $enumStatus;
 		}
 		
 		function contains($strUrl) {
 			$bolRes = $this->wxMap->contains($strUrl);
			Zend_Registry::get('log')->debug('INDEX BOARD: ' . $this->id . ' CONTAINS ' . $strUrl . ' AS ' . ($bolRes ? 'YES' : 'NO'));
 			return $bolRes;
 		}
 		
 		function getAll() {
 			$arrAll = array();
 			foreach ($this->wxMap->find() as $arrEntry) {
 				$arrAll[$arrEntry['url']] = $arrEntry['status'];
 			}
 			return $arrAll;
 		}
 		
 		function size() {
 			$intRes = $this->wxMap->size();
 			Zend_Registry::get('log')->debug('INDEX BOARD: ' . $this->id . ' SIZE AS ' . $intRes);
 			return $intRes;
 		}
 		
 		function __destruct() {
 			$this->wxMap->drop();
 		}
 	}
?>