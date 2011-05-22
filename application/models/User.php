<?php

class Application_Model_User extends Application_Model_Base
{
		protected $_id;
		protected $_username;
    protected $_passwd;
    protected $_fullname						= '';
    protected $_email								= '';
		protected $_quotum_offers				= 0;
    protected $_quotum_indexruns		= 0;
    protected $_current_offers			= 0;
    protected $_current_indexruns		= 0;
    
    protected $items;
}
?>