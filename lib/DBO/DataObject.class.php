<?php

namespace DBO; //Namespace for Don! Briggs Objects

abstract class DataObject
{
    
    private $_versionDate = '01-Mar-2012';
    
    protected static $_tableName   = null;    //Base database table
    protected static $_primaryKey  = null;    //Name of primary key field
    protected static $_fieldMap    = null;    //Mapping of source data fields names to database column names
    protected static $_db          = null;    //Database Insance, either passed in or from session
    protected static $_fieldMeta   = null;    //Field metadata for this table
    protected static $_autoEscape = true;    //Automatic 'escape' on record write, and 'de-escape' on read.

    protected $_rowFields = null;            //Array holding database column names and data values
    protected $_id        = null;            //ID of record. (Should in in Record object)
            
    function __construct($db=null) {
        if(!is_null($db)) {
            static::$_db = $db;
        } elseif (isset ($_SESSION['db'])) {
            static::$_db = $_SESSION['db'];
        } else {
            throw new \Exception("ERROR: Could not set Database object from session");
        }
    }

    public function getInsertId() {
        return $this->_id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function save() {
        if(is_null($this->_id) || $this->_id == 0) {
            $result = $this->_insert();
        } else {
            $result = $this->_update();
        }
        return $result;
    }
    
    public function saveFromArray ($recordArray) {      
        $this->_rowFields = array();
        if(count(static::$_fieldMap)) {          
            $mapFields = true;
            $recordArray = static::fieldNames2dbColumns($recordArray);
        } else {
            //@TODO Add code for non-mapping
            die("ERROR:No Mapping!!!!");
        }
        $this->_rowFields = $recordArray;     
        $pkName = static::$_primaryKey;
        if(key_exists($pkName, $recordArray)) {
            $this->_id = $recordArray[$pkName];
            unset($recordArray[$pkName]);
        }
        $result = $this->save();
        return $result;
    }

    public function getAll() {
        $sql = "SELECT * FROM " .static::$_tableName;
        $result = static::$_db->query($sql);
        return $result;
    }
    
    public function getById($id) {

        $numFields = count(static::$_fieldMeta);
        $fieldList = "";
        $i=1;

        foreach(static::$_fieldMeta as $thisField) {
            
            $fieldList .= $thisField['name'];
            if($i < $numFields) {
                $fieldList .= ", ";
            }
            $i++;
        }
        $sql = "SELECT \n" .$fieldList ."\nFROM " .static::$_tableName ." \n"
             . "WHERE " .static::$_primaryKey ."=" .addslashes($id);
        $result = static::$_db->query($sql);
        $this->_rowFields = static::$_db->fetch_array($result);
        $this->_setId();        
        return $this->_rowFields;        
    }
    
    public function deleteById($id) {
        
            $sql = "DELETE FROM " .static::$_tableName ." \n"
             . "WHERE " .static::$_primaryKey ."=" .addslashes($id);
            $result = static::$_db->query($sql);
            return $result;
    }
    
    public function fetch_array_set($result_set) {
        return static::$_db->fetch_array_set($result_set);    
    }
    
    /**
     * Returns the datatype for a column given it's name in the database table.
     * If an invalid column name is passed, an exception is thrown.
     * 
     * @param string $columnName Column name in the db table to get type or
     * @return string Data type for field in database table
     * @throws \Exception
     */
    
    public function echoField($fieldName) {
        $fieldData = $this->_rowFields[$fieldName];
        if($fieldData == '') {
            echo "&nbsp;";
        } else {
            echo htmlspecialchars($fieldData);
        }
            
    }
    
    public static function getDataType($columnName) {
        if(!isset(static::$_fieldMeta[$columnName])) {
            throw new \Exception("ERROR:: Unable to cast field: $columnName. Column unknown.");
        } else {
            return static::$_fieldMeta($columnName)->type;
        }
    }
        
    public function autoAddSlashes($bool) {
        if($bool) {
            static::$_autoEscape = true;
        } else {
            static::$_autoEscape = false;
        }
    }
    
    /**
     * Accepts an array of data, and returns an array normalized to the database
     * column names. The 'fieldMap' array is used to convert data fields to db
     * fields. Any fields that do not map are dropped. The resultnat array will
     * have only fields and column names appropriate for database insert. 
     * 
     * @param array $fieldArray Array containing data form field names, and table column names they are mapped to
     * @return array Array containing db column names, and their values.
     * @static
     */
    protected static function fieldNames2dbColumns($fieldArray) {
        if(count(static::$_fieldMap)) {$mapFields = true;}
        $dbRec = array();
        
        foreach($fieldArray as $columnName => $columnValue) {
            if($mapFields) {
//                $mapping = static::$_fieldMap;                
                $columnName = static::_mapField($columnName);
            }
            if($columnName) {
                $dbRec[$columnName] = $columnValue;
            }              
        }
        return $dbRec;
    }
    
            
    /**
     * Insert the current contents of '$this->$_rowFields' into the database
     * 
     * @return integer|boolean The insertID of the new record, or false if insert fails
     */
    protected function _insert() {
        $this->_rowFields['date_created'] = time();
        $numFields = count($this->_rowFields);
        $fieldList = "";
        $valueList = "";
        $i=1;
        
        foreach($this->_rowFields as $fieldName => $fieldValue) {
            $fieldList .= $fieldName;
            $valueList .= $this->_castField($fieldName, $fieldValue);
            //Add comma to strings if this is not the last field
            if($i < $numFields) {
                $fieldList .= ", ";
                $valueList .= ", ";
            }
            $i++;
        }
        $sql = "INSERT INTO " .static::$_tableName ." "
             . "(" .$fieldList .") \nVALUES (" .$valueList .")";

        $result = static::$_db->query($sql);
        
        if($result) { 
            $insertId = static::$_db->getInsertId();
            $this->_id = $insertId;
            return $insertId;
         } else {
             return false;
         }
    }
    
    protected function _update() {
     
        $this->_setId(); //remove PK field from _rowFields, and set in object
        $pkName = static::$_primaryKey;  
        $this->_rowFields['date_updated'] = time();
        $numFields = count($this->_rowFields);
        $setList = "";
        $i=1;
        
        foreach($this->_rowFields as $fieldName => $fieldValue) {
            $setList .= "  " .$fieldName ."=" .$this->_castField($fieldName, $fieldValue);
            //Add comma to strings if this is not the last field
            if($i < $numFields) {
                $setList .= ", \n";
            }
            $i++;
        }
        $sql = "UPDATE " .static::$_tableName ." SET \n"
             . $setList ."\n "
             . "WHERE " .$pkName ."=" .$this->_castField($pkName, $this->_id);
        
         $result = static::$_db->query($sql);       
        return $result;
    }
    
    public function setFieldMap($fieldMap) {
        if(!is_array($fieldMap)) { return false; }
        static::$_fieldMap = $fieldMap;
    }

    protected function _setTable($tableName) {
        static::$_tableName = $tableName;
        static::_getTableMeta();
    }


    protected static function _getTableMeta() {

        $columns = array();
        $sql = "   SELECT * FROM information_schema.columns "
             . "WHERE table_schema = '" .static::$_db->getDbName() ."' "
             . "AND table_name = '" .static::$_tableName ."'"  ;
        $rows = static::$_db->query($sql);
        $i=0;

        $recs = static::$_db->fetch_array_set($rows);

        $numFields = count($recs);
        for($i=0; $i<$numFields; $i++) {
            $columnName = $recs[$i]['COLUMN_NAME'];
            $columns[$columnName]['name'] =  $columnName;
            $columns[$columnName]['position'] =  $recs[$i]['ORDINAL_POSITION'];
            $columns[$columnName]['nullable'] =  $recs[$i]['IS_NULLABLE'];
            $columns[$columnName]['dataType'] =  $recs[$i]['DATA_TYPE'];
            if($recs[$i]['COLUMN_KEY'] == 'PRI') {
                static::$_primaryKey = $columnName;
            }
        }       
        static::$_fieldMeta= $columns;
    }
    
    
    /**
     * Accepts a form-data field name, and maps it to a column in the
     * table for this data object. If the form field does not exist in
     * the databse table (example: 'submit' field), returns a null
     * 
     * @static
     * @param string $sourceFieldName Name of form field to be mapped to DB column
     * @return string Column name from database table
     */
    
    protected static function _mapField($sourceFieldName) {
        if(key_exists($sourceFieldName, static::$_fieldMap)) {
            return static::$_fieldMap[$sourceFieldName];
        } else {
            return null;
        }
    }
    
    /**
     * Properly formats field data for insertion into the data table. ie, adds
     * quotes around strings, and does not add quotes around numeric values.
     * 
     * @param string $columnName Name of the column in the table
     * @param mixed $columnData Unformatted column data to be cast
     * @return mixed Column data formatted for insertion into db table
     * @throws \Exception
     */
    protected function _castField($columnName, $columnData) {

        if(!isset(static::$_fieldMeta[$columnName])) {
            throw new \Exception("ERROR:: Unable to cast field: $columnName. Column unknown.");
        }
        //@TODO Add rest of field type codes

        switch (static::$_fieldMeta[$columnName]['dataType']) {
            case 'float':
                return floatval($columnData);
                break;
            case 'int':
                return intval($columnData);
                break;
            case 'varchar':
                if(static::$_autoEscape) {
                    $columnData = addslashes($columnData);
                }
                return "\"" .$columnData ."\"";
                break;
            case 'char':
                //@TODO Add space padding for char data type
                if(static::$_autoEscape) {
                    $columnData = addslashes($columnData);
                }
                return "\"" .$columnData ."\"";
                break;                           
            case 'text':
                if(static::$_autoEscape) {
                    $columnData = addslashes($columnData);
                }
                return "\"" .$columnData ."\"";
                break;            
            default:
                return $columnData;
        }
    }

    protected function _setId() {
        $pkName = static::$_primaryKey;
        if(key_exists($pkName, $this->_rowFields)) {
            $this->_id = $this->_rowFields[$pkName];
            unset($this->_rowFields[$pkName]);
        } else {
            $this->_rowFields[$pkName] = null;
        }
    }
    
}

?>
