<?php

namespace Quore;

class ButtonPanel extends \DBO\AbstractForm
{
    protected $_formName      = 'buttonPanel';
    
    public function __construct() {
        parent::__construct($this->_formName);
      
        $this->_init();
    }
    
    private function _init() {
          $this->setId('navButtonPanel')
                ->setClass('navButtonPanel');
          
          $input_Name = new \DBO\FormElementInput();
          $input_Name->setName('name')
                  ->setClass('formInput')
                  ->setId('txtName')
                  ->setLabel('Name:');
          
          $this->addElement($input_Name);
          
    }
            
}
?>
