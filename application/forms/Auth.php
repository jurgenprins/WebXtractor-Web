<?php

class Application_Form_Auth extends Application_Form_Base
{
		public function init()
    {
    		parent::init();
    		
        $this->addElement('text', 'username', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:200px',
        		'filters'   	=> array('StringTrim'),
            'required'		=> true,
            'label'     	=> 'Your username:',
        )));

        $this->addElement('password', 'passwd', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:200px',
            'filters'    	=> array('StringTrim'),
            'required'   	=> true,
            'label'      	=> 'Password:',
        )));

        $this->addElement('submit', 'login', array_merge(
        	$this->getButConfig(),
        	array(
            'label'    => 'Login',
        )));
    }
}
?>