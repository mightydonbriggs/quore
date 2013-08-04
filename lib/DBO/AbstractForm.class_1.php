<?php

namespace DBO;

abstract class AbstractForm 
{
    protected $_formName      = null;
    protected $_formId        = null;
    protected $_formClass     = null;
    protected $_method        = "POST";
    protected $_action        = "";
    protected $_elements       = array();

    
    public function __construct($formName = null, $formMethod = null, $formAction = null, $formClass = null, $formId = null) {
        
        $this->_elements = array();
        
        if(!is_null($formName)) {
            $this->setName($formName);
        }
        
        if(!is_null($formMethod)) {
            $this->setMethod($formMethod);
        }
        
        if(!is_null($formAction)) {
            $this->setAction($formAction);
        }
        
        if(!is_null($formClass)) {
            $this->setClass($formClass);
        }
        
        if(!is_null($formId)) {
            $this->setId($formId);
        }
    }
    
    public function setName($formName) {
        $this->_formName = $formName;
        return $this;
    }
    
    public function setId($formId) {
        $this->_formId = $formId;
        return $this;
    }
    
    public function setClass($formClass) {
        $this->_formClass = $formClass;
        return $this;
    }
    
    public function setMethod($method) {
       
        if(strtolower($method) == 'get') {
            $this->_method = $method;            
        } else {
            $this->_method = 'POST';            
        }
        return $this;
    }
    
    public function setAction($formAction) {
        //@TODO Add $methodName validation
        $this->_action = $formAction;
    }

    public function addElement($formElement) {
        
        if($formElement instanceof AbstractFormElement) {
            $numElements = array_push($this->_elements, $formElement);  //Add the element to the array
            $index = $numElements -1;         //Get the index of the element we just added
            $this->_elements[$index]->setIndex($index);        
        } else {
            throw new \Exception("ERROR: Can't add element to form. Only FormElements can be added");
        }
    }
    
    public function render() {
        echo $this->getHtml();
    }    
    
    public function getHtml() {
        return $this->_buildHtml();
    }
    
    public function getElementByIndex($index) {
        $index = intval($index);
        if(array_key_exists($index, $this->_elements)) {
            return $index;
        } else {
            return false;
        }
    }
    
    public function getElementByName($elementName) {
        
        $elementName = strtolower(trim($elementName));
        $numElements = count($this->_elements);
        for($i=0; $i < $numElements; $i++) {
            $thisName = $this->_elements[$i]->getElementName();
            if($elementName == strtolower($thisName)) {
                return $i;
            }
        }
        return false;
    }
    
    public function setValueByName($elementName, $value) {
        $elementIndex = $this->getElementByName($elementName);
        if($elementIndex === false) {
            throw new \Exception("ERROR: Could not find form element: $elementName");
        }
        
        $this->_elements[$elementIndex]->setValue($value);
        return $this;
    }
    
    public function setValueByIndex($elementIndex, $value) {
        $elementIndex = $this->getElementByIndex($elementIndex);
        if($elementIndex === false) {
            throw new \Exception("ERROR: Could not find form element with index of: $elementIndex");
        }
        
        $this->_elements[$elementIndex]->setValue($value);
        return $this;
    }
    
    public function setValuesByArray(array $arrayValues) {
        foreach($arrayValues as $key => $value) {
            $elementIndex = $this->getElementByName($key);
            if($elementIndex !== false) {
                $this->setValueByIndex($elementIndex, $value);
            }
        }
    }
    
    protected function _buildHtml() {
        $html = "\n<div class='dbo_dataFormDiv' id='dbo_" .$this->_formId ."Div'>\n"
              . "  <form" 
              . $this->_getFormName()
              . $this->_getFormAction()
              . $this->_getFormMethod()
              . $this->_getFormClass()
              . $this->_getFormId()
              .">\n";
        
        foreach($this->_elements as $element) {
            $html .= $element->getHtml();
        }
        $html .= "  </form>\n</div?\n";
        return $html;
    }
    
    protected function _getFormName() {
        if(!empty($this->_formName)) {
            return " name='" .$this->_formName ."'";
        } else {
            return "";
        }
    }

    protected function _getFormAction() {
        if(!empty($this->_action)) {
            return " action='" .$this->_action ."'";
        } else {
            return " action=''";
        }
    }

    protected function _getFormMethod() {
        if(!empty($this->_method)) {
            return " method='" .$this->_method ."'";
        } else {
            return "";
        }
    }
    
    protected function _getFormClass() {
        if(!empty($this->_formClass)) {
            return " class='" .$this->_formClass ."'";
        } else {
            return "";
        }
    }
    
    protected function _getFormId() {
        if(!empty($this->_formId)) {
            return " id='" .$this->_formId ."'";
        } else {
            return "";
        }
    }
}
?>
