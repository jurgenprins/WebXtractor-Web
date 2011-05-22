<?php

class Application_Model_BaseIntersect extends Application_Model_Base
{
    public function getId() {
    	$id = '';
    	foreach(array_keys(get_class_vars(get_class($this))) as $varName) {
    		if (substr($varName, 0, 3) == '_id') {
    			if ($id) $id .= '-';
    			$id .= $this->$varName;
    		}
    	}
    	return $id;
    }
    
    public function setId() {}
}
?>