<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        /*
        foreach($user->items as $item) {
        	
        	$item = $itemSvc->get($item, 
        		Application_Service_Item::GET_INCLUDE_ATTRS |
        		Application_Service_Item::GET_INCLUDE_DATASOURCES);
        	
        	print $item->getName() . '<br>';	
        	foreach($item->getItemAttributes() as $attribute) {
        		print ' &nbsp; ATTR ' . $attribute->getName() . '=' . $attribute->getVal() . '<br>';
        	}
        	
        	foreach($item->getDatasources() as $datasource) {
        		print ' &nbsp; DS ' . $datasource->getName() . "<br>";
        	
        		$attributes = $datasourceDao->getDatasourceAttributes($datasource);
        		print_r($attributes);
        		
						$extractor = $datasourceDao->getExtractor($datasource);
						print " extractor = " . $extractor->getName() . "<br>";
						
						$attributes = $extractorDao->getExtractorAttributes($extractor);
        		print_r($attributes);
        		
        	}
        	
        	$user = $itemDao->getUser($item);
        	print "user = " . $user->getFullname() . "<br>";
        	
        	$parent = $itemDao->getParent($item);
        	print "parent = " . $item->getName() . "<br>";
        }
        */
        //$userDao->save($user);
    }

}
?>