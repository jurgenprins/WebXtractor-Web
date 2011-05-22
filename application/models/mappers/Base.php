<?php

class Application_Model_Mapper_Base
{
		protected $_modelName;
		protected $_dbTable;
		
		public function __construct($modelName) {
			$this->_modelName = $modelName;
		}
		
  	public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided: ' . $dbTable);
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_' . ucfirst($this->_modelName));
        }
        return $this->_dbTable;
    }
    
    public function getModel($modelName = '', array $options = null) {
    		if ('' == $modelName) {
    			$modelName = $this->_modelName;
    		}
        $modelClass = 'Application_Model_' . ucfirst($modelName);
        $model = new $modelClass($options);
        if (!$model instanceof Application_Model_Base) {
            throw new Exception('Invalid model name provided: ' . $modelName);
        }
        return $model;
    }
    
    public function countDependency(array $criteria, $dependency = null) {
    	$info = $this->getDbTable()->info();
    	$select = $this->getDbTable()->select();
    	
  		foreach ($criteria as $name => $val) {
  			$select->where($name . '= ?', $val);
  		}
  		
  		$resultSet = $this->getDbTable()->fetchAll($select);
  		
  		$entries  = array();
  		foreach ($resultSet as $row) {
				if (is_array($dependency)) {
					foreach($dependency as $destTable => $intersectTable) {
  					return $row->findManyToManyRowset(
  						'Application_Model_DbTable_' . ($dependency = ucfirst($destTable)),
  						'Application_Model_DbTable_' . ucfirst($intersectTable))->count();
  				}
				} else {
					return $row->findDependentRowset(
						'Application_Model_DbTable_' . ucfirst($dependency))->count();
				}
			}
    }
    
    public function find(array $criteria, array $dependencies = null, array $parents = null, $start = 0, $limit = 999) {
    		$select = $this->getDbTable()->select();
    	
    		foreach ($criteria as $name => $val) {
    			if (is_array($val)) {
    				$select->where($name . 'in ?', join(',', $val));
    			} else {
    				$select->where($name . '= ?', $val);
    			}
    		}
    		
    		$resultSet = $this->getDbTable()->fetchAll($select);
    		
    		$entries  = array();
        foreach ($resultSet as $row) {
        		$entry = $this->getModel('', $row->toArray());
        	
        		if (is_array($dependencies)) {
        			foreach($dependencies as $dependency) {
        				$intersectTable = null;
        				if (is_array($dependency)) {
        					foreach($dependency as $destTable => $intersectTable) {
	        					$depResultSet = $row->findManyToManyRowset(
	        						'Application_Model_DbTable_' . ($dependency = ucfirst($destTable)),
	        						'Application_Model_DbTable_' . ucfirst($intersectTable),
	        						null, null, $this->getDbTable()->select()->limit($limit, $start));
	        					break;
	        				}
        				} else {
        					$depResultSet = $row->findDependentRowset(
        						'Application_Model_DbTable_' . ucfirst($dependency),
        						null, $this->getDbTable()->select()->limit($limit, $start));
        				}
        				$depEntries = array();
        				foreach ($depResultSet as $depRow) {
        					$depEntry = $this->getModel(ucfirst($dependency), $depRow->toArray());
        					$depEntries[] = $depEntry;
        				}
        				$methodName = 'set' . ucfirst($dependency) . 's';
        				$entry->$methodName($depEntries);
        			}
        		}
        		
        		if (is_array($parents)) {
        			foreach($parents as $parent) {
        				if (is_array($parent)) {
        					foreach($parent as $parentName => $parentVar) {
        						$parent = $parentName;
        						$methodName = 'set' . ucfirst($parentVar);
        						break;
        					}
        				} else {
        					$methodName = 'set' . ucfirst($parent);
        				}
        				$parentRow = $row->findParentRow($s = 'Application_Model_DbTable_' . ucfirst($parent));
        				if (!is_null($parentRow)) {
	        				$parentEntry = $this->getModel(ucfirst($parent), $parentRow->toArray());
	        				$entry->$methodName($parentEntry);
	        			}
        			}
        		}
        		
        	  $entries[] = $entry;
        }

        return $entries;
    }

    public function get($id = 0, array $dependencies = null, array $parents = null, $start = 0, $limit = 999) {
    		$info = $this->getDbTable()->info();
    		
    		$results = $this->find(
    			array($info['primary'][1] => $this->getDbTable()->getAdapter()->quote($id,'INTEGER')),
    			$dependencies,
    			$parents,
    			$start, $limit);
    			
    		if (0 == count($results)) {
    			return null;
    		}
    		
    		return $results[0];
    }
    
    public function save(&$model)
    {
    		if (!$model instanceof Application_Model_Base) {
            throw new Exception('Invalid model name provided: ' . $modelName);
        }
        
        $info = $this->getDbTable()->info();
        $data = $model->getOptions();
     
        $where = array();
        foreach($info['primary'] as $primkey) {
    			$where[] = $this->getDbTable()->getAdapter()->quoteInto($primkey . ' = ?', $data[$primkey]);
    		}
    		$resultSet = $this->getDbTable()->fetchAll($where);
    		
      	if (count($resultSet) > 0) {
     			$this->getDbTable()->update($data, $where);
     			return true;
     		}
     		
     		foreach($info['primary'] as $primkey) {
      		if ($info['metadata'][$primkey]['IDENTITY']) {
          	unset($data[$primkey]);
          }
       	}
       	
       	$this->getDbTable()->insert($data);
       	$model->setId($this->getDbTable()->getAdapter()->lastInsertId());
    }
    
		public function delete(&$model)
    {
    		if (!$model instanceof Application_Model_Base) {
            throw new Exception('Invalid model name provided: ' . $modelName);
        }
        
        $info = $this->getDbTable()->info();
        $data = $model->getOptions();
        
        $where = array();
        foreach($info['primary'] as $primkey) {
    			$where[] = $this->getDbTable()->getAdapter()->quoteInto($primkey . ' = ?', $data[$primkey]);
    		}
	      
    		$resultSet = $this->getDbTable()->delete($where);
		}
 
}
?>