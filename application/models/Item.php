<?php

class Application_Model_Item extends Application_Model_Base
{
		protected $_id;
		protected $_name;
    protected $_idparent;
    protected $_iduser;
    
    protected $itemattributes;
    
    protected $datasources;
    protected $itemdatasources;
    
    protected $offerscount;
    protected $offers;
    protected $itemoffers;
    
    protected $user;
    protected $parent;
    protected $children;
}
?>