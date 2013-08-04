<?php

namespace DBO;  //Namespace for Don! Briggs Objects

/**
 * Display an array record set as a nicely formatted HTML table. Allows setting
 * of CSS classes for various elements
 */
class Tableizer {
    
    private $_html        = null;
    private $_records     = null;
    private $_numCols     = null;
    private $_title       = null;
    private $_tableClass  = null;
    private $_tableId     = null;
    private $_tableTitleClass = null;
    private $_fieldMeta   = null;


    public function __construct($recordArray = null) {
        if(!is_null($recordArray)) {
            $this->_setRecordArray($recordArray);
        }
    }
    
    public function render() {
        $this->getHTML();
        echo $this->_html;
    }
    
    public function getHTML() {
        $html = "";
        $head = $this->_getTableHead();        
        $body = $this->_getTableBody();
        $tableClass = $this->_getTableClass();
        $html = "\n<table$tableClass>\n" . $head .$body ."</table>\n";
        $this->_html = $html;
        return $this->_html;
    }
    
    public function setTableClass($class) {
        $this->_tableClass = $class;
    }
    
    public function setTableTitleClass($class) {
        $this->_tableTitleClass = $class;
    }
    
    public function setTitle($title) {
        $this->_title = $title;
    }
    
    
    public function setTableId($id) {
        $this->_tableId= $id;
    }
    
    public function setColumnTitles(array $columnTitles) {
        
        $columnNumber = null;
        
        foreach($columnTitles as $columnName => $title) {
        
            if(is_numeric($columnName)) {
                //Field was specified by number
                if($columnName < $this->_numCols) {
                    $columnNumber = $columnName;
                } else {
                    throw new \Exception("Column Number is out of bounds");
                }
            } else {
               //Field was specified by name
               for($i=0; $i < $this->_numCols; $i++) {
                   if($this->_fieldMeta[$i]['fieldName'] == $columnName) {
                       $columnNumber = $i;
                       break;
                   }
               }
            }
            
            if(is_null($columnNumber)) {
                throw new Exception("Could not find column: $columnName");
            }
            
            $this->_fieldMeta[$columnNumber]['colTitle'] = trim(htmlentities($title));
        }
    }
    
    protected function _getColumnTitles() {
        $html = "    <tr>\n";
        for($i=0; $i<$this->_numCols; $i++) {
            $colData = $this->_fieldMeta[$i];
            if($colData['showColHead'] == true) {
                $classHTML = $this->_getColumnTitleClass($colData);
                if(key_exists('colTitle', $colData)) {
                    $html .= "      <th$classHTML>" .trim(htmlspecialchars($colData['colTitle'])) ."</th>\n";
                } else {
                    $html .= "      <th$classHTML>" .trim(htmlspecialchars($colData['fieldName'])) ."</th>\n";                    
                }
            } else {
                $html .= "&nbsp;";
            }
        }
        $html .= "    </tr>\n";
        return $html;
    }

    protected function _getTableClass() {
        
        if(!empty($this->_tableClass)) {
            return " class='" .$this->_tableClass ."'";
        } else {
            return '';
        }
    }
    
    protected function _getTableTitleClass() {
        
        if(!empty($this->_tableTitleClass)) {
            return " class='" .$this->_tableTitleClass ."'";
        } else {
            return '';
        }
    }
    
    protected function _getColumnTitleClass(array $fieldMeta) {
        $html = '';
        if($fieldMeta['showHeadClass'] == true) {
            $html = " class='" .$fieldMeta['colHeadClass'] ."'";
        }
        return $html;
    }
    
    protected function _getColumnClass(array $fieldMeta) {
        $html = '';
        if($fieldMeta['showRowClass'] == true) {
            $html = " class='" .$fieldMeta['rowClass'] ."'";
        }
        return $html;
    }
    
    protected function _getTableHead() {
        $tableTitleClass = $this->_getTableTitleClass();
        $html = "    <tr><th$tableTitleClass colspan='" .$this->_numCols ."'>"
              . $this->_title ."</th></tr>\n";
        $html = "  <thead>\n" .$html ."  </thead>\n";
        return $html;        
    }
    
    protected function _getTableBody() {
        $numRows = count($this->_records);
        if(!$numRows) {
            $html ="<tr><td>[No Records in Dataset]</td><tr>";
        } else {
            $html = "";
            $html .= $this->_getColumnTitles();
            for($i=0; $i<$numRows; $i++) {
                $html .=$this->_getTableRow($this->_records[$i]);
            }
            $html = "  <tbody>\n" .$html ."  </tbody>\n";
            return $html;
        }
    }
    
    protected function _getTableRow($rowRec) {

        $html = "";
        $i=0;
        foreach($rowRec as $colName => $colVal) {
            $fieldMeta = $this->_fieldMeta[$i];
            $classHTML = $this->_getColumnClass($fieldMeta);
            $html .= "      <td$classHTML>" .$colVal ."</td>\n";
            $i++;
        }
        $html = "    <tr>\n" .$html ."    </tr>\n";
        return $html;
    }
    
    protected function _setRecordArray($recordArray) {

        if(!is_array($recordArray)) {
            throw new \Exception("Parameter 'recordArray' must be of type Array");
        }
        
        if(count($recordArray)) {
            $this->_records = $recordArray;
            $this->_numCols = count($recordArray[0]);
            $this->_setFieldMeta();
        } else {
            $this->_records = null;
            $this->_numCols = 0;
            $this->_fieldMeta = array();
        }
    }
    
    protected function _setFieldMeta() {
        if(is_array($this->_records)) {
            $rec = $this->_records[0];    //Grab first record out of list
            $i=0;
            $this->_fieldMeta = array();
            foreach ($rec as $fieldname => $value) {
                $this->_fieldMeta[$i]['fieldNumber'] = $i;
                $this->_fieldMeta[$i]['fieldName'] = $fieldname;
                $this->_fieldMeta[$i]['showColHead'] = true;
                $this->_fieldMeta[$i]['showHeadClass'] = true;
                $this->_fieldMeta[$i]['colHeadClass'] = 'col_title_' .$fieldname;
                $this->_fieldMeta[$i]['showRowClass'] = true;
                $this->_fieldMeta[$i]['rowClass'] = 'col_' .$fieldname;
                $i++;
            }
        }
    }
    

}

?>
