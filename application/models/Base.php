<?php

class Application_Model_Base
{
		public function __construct(array $options = null)
    {
        if (is_array($options)) {
        	  $this->setOptions($options);
        }
    }
 
 		public function __call($name, $arguments) {
 			switch(substr($name, 0, 3)) {
 				case 'get':
 					$varName = '_' . strtolower(substr($name, 3));
	 					if (in_array($varName, array_keys(get_class_vars(get_class($this))))) {
 						return $this->$varName;
 					} else {
 						$varName = strtolower(substr($name, 3));
 						if (in_array($varName, array_keys(get_class_vars(get_class($this))))) {
 							return $this->$varName;
 						} else {
 							throw new Exception('Non-existing property: ' . $varName);
 						}
 					}
 					break;
 				case 'set':
 					$varVal  = (is_array($arguments) && (count($arguments) > 0)) ? $arguments[0] : null;
 					$varName = '_' . strtolower(substr($name, 3));
 					if (in_array($varName, array_keys(get_class_vars(get_class($this))))) {
 						$this->$varName = is_string($varVal) ? substr($varVal, 0, 255) : $varVal;
	 				} else {
	 					$varName = strtolower(substr($name, 3));
	 					if (in_array($varName, array_keys(get_class_vars(get_class($this))))) {
 							$this->$varName = is_string($varVal) ? substr($varVal, 0, 255) : $varVal;
	 					} else {
 							throw new Exception('Non-existing property: ' . $varName);
 						}
 					}
	 				break;
 				default:
 			}
 		}
 	
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        $this->$method($value);
    }
 
    public function __get($name)
    {
    	  $method = 'get' . $name;
    	  return $this->$method();
    }
 
    public function setOptions(array $options)
    {
    	  foreach ($options as $key => $value) {
    	  		if (is_array($value)) continue;
    	  		try {
            	$method = 'set' . ucfirst($key);
            	$this->$method($value);
            } catch (Exception $e) {}
        }
        return $this;
    }
    
    public function getOptions() {
    	$options = array();
    	foreach(array_keys(get_class_vars(get_class($this))) as $varName) {
				if (substr($varName, 0, 1) == '_') {
					$method = 'get' . substr($varName, 1);
					$varVal = $this->$method();
					if (is_array($varVal)) continue;
					$options[strtolower(substr($varName, 1))] = $varVal;
				}
    	}
    	return $options;
    }
}
?>