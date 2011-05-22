<?php

class Application_Service_User extends Application_Service_Base
{
		protected $userDao = null;
		
		const			DEFAULT_QUOTUM_OFFERS			= 100; 
		const			DEFAULT_QUOTUM_INDEXRUNS	= 10; 
		
    const			GET_INCLUDE_NONE		= 0;
    const 		GET_INCLUDE_ITEMS		= 1;
    
    protected function __construct() {
    	$this->userDao = new Application_Model_Mapper_User();
    }
    
    public static function getInstance()
    {
        return parent::getInstance(get_class());
    }
    
    public function getUserDao() {
    	return $this->userDao;
    }
    
    public function createUser() {
    	return $this->userDao->getModel('', array(
    		'quotum_offers' 		=> self::DEFAULT_QUOTUM_OFFERS,
    		'quotum_indexruns'	=> self::DEFAULT_QUOTUM_INDEXRUNS));
    }
    
    public function getUser($mixedUser, $optIncludes = self::GET_INCLUDE_NONE) {
    	if ($mixedUser instanceof Application_Model_User) {
    		$user = $mixedUser;
    	} else if (is_numeric($mixedUser)) {
    		$user = $this->userDao->get($mixedUser);
    	} else {
    		throw new Exception ('Not a valid user identifier');
    	}
     	
    	if ($optIncludes & self::GET_INCLUDE_ITEMS) {
    		$user->setItems($this->userDao->getItems($user));
    	}
    	
    	return $user;
		}
		
		public function findUserByUsername($username) {
			$res = $this->userDao->find(array('username' => $username));
			if (count($res) <= 0) {
				return null;
			}
			return $res[0];
		}
		
		public function saveUser(Application_Model_User &$user) {
    	return $this->userDao->save($user);
		}
		
		public function removeUser(Application_Model_User &$user) {
			return $this->userDao->delete($user);
		}
}
?>