<?php

namespace DBO;

class FormElementSubmit extends  \DBO\AbstractFormElement
{
    protected $_type      = 'input';
    protected $_inputType = 'submit';
    protected $_appendBr  = false;
    
    public function __construct() {
    }
    
    public function getHtml() {
        
        $html = $this->_getLabel()
              . "     <input"
              . $this->_getName()
              . $this->_getInputType()
              . $this->_getClass()
              . $this->_getId()
              . $this->_getValue()
              . "/>\n";
        if($this->_appendBr) {
            $html .= "     <br />\n";
        }
        return $html;
    }
    
    protected function _getInputType() {
        if(!empty($this->_inputType)) {
            return " type='" .$this->_inputType ."'";
        } else {
            return "";
        }
    }
}
?>
