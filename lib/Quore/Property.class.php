<?php

namespace Quore;

class Property extends \DBO\DataObject
{
    public function __construct($db = null, $fieldValues = null) {
        parent::__construct($db);
        $this->_setTable('property');
        //If values array was passed, set values now.
        if(!is_null($fieldValues)) {
            $this->setFieldsFromArray($fieldValues);
        }
    }
    
    
    // Static function to return service type name, given 'isFullService' field
    public static function getServiceTypeName ($isFullService) {
        $db = \DBO\MySqlDatabase::getInstance();
        $sql = "SELECT definition AS serviceType "
             . "FROM dictionary "
             . "WHERE class='SERVICE_TYPE' AND term = $isFullService";
        $result = $db->query($sql);
        $row = $db->fetch_array($result);
        return $row['serviceType'];
        
    }
    
    //Static function to return region name given 'region_id' field
    public static function getRegionName ($region_id) {
        $db = \DBO\MySqlDatabase::getInstance();
        $sql = "SELECT name AS regionName "
             . "FROM region "
             . "WHERE id = $region_id";
        $result = $db->query($sql);
        $row = $db->fetch_array($result);
        return $row['regionName'];
    }
    
    public function getFieldValueArray($id) {
        
        $propertyRec = array();
        $id = intval($id);
        if($id) {
            $propertyRec = $this->getById($id);
            $propertyRec['id'] = $this->getId();
        } else {
            $propertyRec = $this->setFieldsFromArray($_REQUEST);
        }
        return $propertyRec;
    }

    
    
    public function setFieldsFromArray(array $fieldValues) {
        
        //Filter out any elements tha are not databas columns
        $initialValues = array_intersect_key($fieldValues, static::$_fieldMeta);
        if(count($initialValues) > 1) {
            foreach($initialValues as $colName => $colValue) {
                $this->_rowColumns[$colName] = $colValue;
                //@TODO Following line bypasses casting. Write nicer solution
                self::$_fieldMeta[$colName]['value'] = $colValue;
            }
            return $this->_rowColumns;
        }
    }
}    
?>
