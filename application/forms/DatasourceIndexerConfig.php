<?php

class Application_Form_DatasourceIndexerConfig extends Application_Form_Base
{
		public function init()
    {
    		parent::init();
    		
        $this->addElement('text', 'MaxLinksToFollow', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:50px',
            'validators' => array(
                'Int',
                array('StringLength', false, array(1, 3)),
            ),
            'required'	=> true,
            'label'      => 'Max links to follow',
        )));
        
        $this->addElement('text', 'RunEvery', array_merge(
        	$this->getTextConfig(), 
        	array(
        		'style'				=> 'width:50px',
            'validators' => array(
                'Int',
                array('StringLength', false, array(1, 3)),
            ),
            'label'      => 'Run every X hours',
        )));
        
        $this->addElement('submit', 'save', array_merge(
					$this->getButConfig(), 
					array(
						'label' => 'Save'
				)));
    }
}
?>