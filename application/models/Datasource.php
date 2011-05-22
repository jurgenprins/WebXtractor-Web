<?php

class Application_Model_Datasource extends Application_Model_Base
{
		protected $_id;
		protected $_url;
    protected $_name;
    protected $_idextractor;
    protected $_updated;
    
    protected $datasourceattributes;
    protected $itemdatasources;
    
    protected $extractor;
    protected $items;
    
    public function setUrl($url) {
    	if (!strstr($url, '://')) {
    		$url = 'http://' . $url;
    	}
    	$this->_url = substr($url, 0, 255);

    	if (!$this->_name) {
    		$this->_name = str_replace('http://', '' , $this->_url);
    	}
    }
}
?>