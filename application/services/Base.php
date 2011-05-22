<?
	abstract class Application_Service_Base {	
		private static $_instances = array();
		
	 	protected function __construct() {}
 		protected function __clone() {}

		public static function getInstance($clazz) {
			if(!isset(self::$_instances[$clazz])) {
      	self::$_instances[$clazz] = new $clazz();
      }
      return self::$_instances[$clazz];
    }
	}
?>