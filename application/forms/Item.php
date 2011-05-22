<?php

class Application_Form_Item extends Application_Form_Base
{
    public function init()
    {
    	parent::init();

    	$this->addElement('text', 'name', array_merge(
				$this->getTextConfig(), 
				array(
					'required'	=> true,
					'label' => 'Name'
			)));
			
			$this->addElement('submit', 'save', array_merge(
				$this->getButConfig(), 
				array(
					'label' => 'Save'
			)));
	}
}
?>