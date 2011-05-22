<?php

class Application_Form_User extends Application_Form_Base
{
		public function init()
    {
    		parent::init();
    			
        $this->addElement('text', 'username', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:200px',
        		'filters'    => array('StringTrim'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(2, 20)),
            ),
            'required'	=> true,
            'label'      => 'Username<super>*</super>:',
        )));

        $this->addElement('password', 'passwd', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:200px',
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'required'   => true,
            'label'      => 'Password<super>*</super>:',
        )));
        
    		$this->addElement('text', 'email', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:200px',
            'filters'    => array('StringTrim'),
            'required'   => false,
            'validators' => array(
                'EmailAddress'
            ),
            'label'      => 'Email (to be notified):',
        )));

        $this->addElement('text', 'fullname', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:200px',
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Full name (to display to others):',
        )));
    }
}
?>