<?php

namespace Quore;

class Property extends \DBO\DataObject
{
    public function __construct($db = null) {
        parent::__construct($db);
        $this->_setTable('property');
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
        $id = intval($id);
        $propertyRec = $this->getById($id);
        $propertyRec['id'] = $this->getId();
        return $propertyRec;
    }
}
?>
