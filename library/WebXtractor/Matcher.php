<?
	class WebXtractor_Matcher {
 		private static $instance;
    
		protected $stopwords = null;
 		
 		private function __construct($lang = 'nl') {
 			$this->stopwords = $this->splitString(file_get_contents(APPLICATION_PATH . '/configs/stopwords.' . $lang . '.txt'));
 		}
 		private function __clone() {}

		public static function getInstance($lang = 'nl') {
        if (!self::$instance[$lang] instanceof self) {
            self::$instance[$lang] = new self($lang);
        }
        return self::$instance[$lang];
    }

		public function splitString($str) {
			return preg_split('/[^A-Za-z0-9_\-.]+/', $str);
		}
		
		public function compile($str, $mixedStopWords = '') {
			if (!$str)				return array();
			
			if (is_array($mixedStopWords)) {
				$aStopWords 	= array_merge($mixedStopWords, $this->stopwords);
			} else {
				if (strlen($mixedStopWords)) {
					$aStopWords 	= array_merge($this->splitString(strtolower($mixedStopWords)), $this->stopwords);
				} else {
					$aStopWords		= $this->stopwords;
				}
			}
			
			$aPostedWords = $this->splitString($str);
			
			$aWords = array();
			foreach($aPostedWords as $strWord) {
				$strWord = trim(strtolower($strWord));
				if (in_array($strWord, $aStopWords)) {
					continue;
				}
				if ((strlen($strWord) < 2) && ((0 + $strWord) == 0)) {
					continue;
				}
				$aWords[$strWord] = true;
			}
			
			return array_keys($aWords);
		}
		
		public function match(array $aSource, array $aTarget, $fltMinDistanceRatio = 0.3) {
			$nTargetWords	= count($aTarget);
			
			$nMatchedWords			= 0;
			$nScore							= 0;
			foreach($aTarget as $sTargetWord) {
				foreach($aSource as $sSourceWord) {
					if (!$sTargetWord || !$sSourceWord) continue;
					if (!strcmp($sTargetWord, $sSourceWord)) {
						$nMatchedWords++;
						continue;
					}
					$nDistance	= levenshtein($sTargetWord, $sSourceWord);
					$nLen				= max(strlen($sTargetWord), strlen($sSourceWord));
					if (($nDistance / $nLen) <= $fltMinDistanceRatio) {
						$nMatchedWords++;
					}
				}
			}
			$nScore	= round(($nMatchedWords / $nTargetWords) * 100);
			return $nScore;
		}
	}
?>