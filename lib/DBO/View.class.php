<?php

namespace DBO;

class View
{
    protected $_templateFile = null;
    protected $_data         = array();  
    public    $content       = null;


    public function __construct($template = null) {
        if(!is_null($template)) {
            $this->_setTemplate($template);
        }
    }
    
    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }
    
    public function __get($propName) {
//print "PropName: $propName \n"        ;
        if(key_exists($propName, $this->_data)) {
//print "Found it!\n";            
            return $this->_data[$propName];
        } else {
            throw new \Exception("ERROR: The property: $propName was not found");
        }
    }
    
    public function setTemplate($templateFile) {
        $this->_setTemplate($templateFile);
    }
    
    public function render() {
        include($this->_templateFile);
    }
    
    protected function _setTemplate($template) {
        $templateFile = realpath($_SESSION['templatePath'] .DIRECTORY_SEPARATOR . $template);
        
        if($templateFile === false) {
        print "YUP";
            throw new \Exception("Template file: $template could not be located");
        } elseif (!is_readable($templateFile)) {
            throw new \Exception("Template file: $template was found, but is not readable");            
        }
        
        $this->_templateFile = $templateFile;
    }
}
?>
