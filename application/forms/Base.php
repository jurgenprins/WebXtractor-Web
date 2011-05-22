<?php

class Application_Form_Base extends Zend_Form
{
		protected $elDecorators		= null;
		protected $elTextConfig 	= null;
		protected $elUrlConfig 		= null;
		protected $elButConfig 		= null;
		
    public function init()
    {
    	$this->elDecorators = array(
				'ViewHelper',
				'Errors',
				array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'x-form-element')),
				array('Label', array('tag' => 'span', 'placement' => 'prepend')),
				array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'x-form-item')));
    				
    	$this->elTextConfig = array(
				//'validators' 	=> array(array('alnum', false, true)),
				'style'				=> 'width:100%',
				'filters'  		=> array('StringToLower', 'StringTrim'),
				'decorators'	=> $this->elDecorators);
			
			$this->elUrlConfig = array(
				'style'				=> 'width:100%',
				'filters'  		=> array('StringTrim'),
				'decorators'	=> $this->elDecorators);
			
			$this->elButConfig = array(
			  'ignore'   => true,
				'decorators'	=> array(
    				'ViewHelper',
				    array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'x-btn')),
				    array(array('label' => 'HtmlTag'), array('tag' => 'span', 'placement' => 'prepend')),
				    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'x-form-item'))));
				
			// TODO: figure out how to make this work in multiple forms on same page
			// $this->addElement('hash', 'ck', array('salt' => 'unique'));
			
			$elemClass = new Zend_Form_Element_Hidden('class');
			$elemClass->setValue(str_replace('Application_Form_', '', get_class($this)));
			$elemClass->setIgnore(true);
			$this->addElement($elemClass);
			
			$this->setDecorators(array(
				    'FormElements',
				    'Form',
				    array('HtmlTag', array('tag' => 'div', 'class' => 'x-form', 'style' => 'width:100%')),
				    array('Description', array('class' => 'x-form-item', 'placement' => 'prepend')),
		    	));
	}
	
	public function getElementDecorators() {
		return $this->elDecorators;
	}
	
	public function getTextConfig() {
		return $this->elTextConfig;
	}
	
	public function getUrlConfig() {
		return $this->elUrlConfig;
	}
	
	public function getButConfig() {
		return $this->elButConfig;
	}
}
?>