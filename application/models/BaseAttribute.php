<?php

class Application_Model_BaseAttribute extends Application_Model_Base
{
		const			TYPE_BOOL		= 1;
		const			TYPE_INT		= 2;
		const			TYPE_STRING	= 3;
		
		protected $_name;
    protected $_val;
    protected $_type;
    
    public function __construct($mixedOpts, $val = '', $type = self::TYPE_STRING)
    {
    		if (is_array($mixedOpts)) {
    			$name 	= $mixedOpts['name'];
    			$val 		= $mixedOpts['val'];
    			$type 	= $mixedOpts['type'];
    		} else {
    			$name		= $mixedOpts;
    		}
        $this->setName($name);
        $this->setVal($val);
        $this->setType($type);
    }
    
    public function getId() {
    	$id = $this->getName();
    	foreach(array_keys(get_class_vars(get_class($this))) as $varName) {
    		if (substr($varName, 0, 3) == '_id') {
    			$id .= '-' . $this->$varName;
    		}
    	}
    	return $id;
    }
    
    public function setId() {}
}
?>