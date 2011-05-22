<?php

class Application_Form_DatasourceIndexerRun extends Application_Form_Base
{
		public function init()
    {
    		parent::init();
    		
        $this->addElement('submit', 'run', array_merge(
					$this->getButConfig(), 
					array(
						'label' => 'Run (asap)'
				)));
    }
}
?>