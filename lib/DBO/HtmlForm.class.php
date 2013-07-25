<?php

namespace DBO;

class HtmlForm extends \DBO\AbstractForm
{
    protected $_rowQuery;     //Query to be run against table for dataset
    protected $_html = null;
    protected $_formClass;
    protected $_rowClass;     //Default row class, if not overridden
    protected $_method = "POST";
    protected $_action = "";

    
    public function setFormClass($formClassName) {
        $this->_formClass = $formClassName;
    }
    
    public function setFormMethod($formMethodName) {
        //@TODO Add $formMethodName validation
        $this->_method = $formMethod;
    }

    public function render() {
        echo $this->_getHtml();
    }    
    
    protected function _getHtml() {
        return $this->_html;
    }
}
?>
