<?php

namespace Quore;

class PropertyList
{
    protected $_db = null;         //Holds db object used for queries
    protected $_records = array();    //Array of records returned by query
    
    public function __construct() {
        if(isset($_SESSION['db']) ) {
            $this->_db = $_SESSION['db'];
        } else {
            throw new \Exception("Database object not found in session");
        }
        $this->query();
    }

    public function query() {
        $sql = "SELECT p.id, p.name, p.brand, r.name AS region, p.phone, \n"
             . "d.definition AS serviceType, p.url \n"
             . "FROM property AS p \n"
             . "LEFT JOIN region AS r \n"
             . "ON p.region_id = r.id \n"
             . "LEFT JOIN dictionary AS d \n"
             . "ON p.isFullService = d.term AND d.class = 'SERVICE_TYPE' \n"   
                ;
        $result = $this->_db->query($sql);
        $this->_records = $this->_db->fetch_array_set($result);
        $this->_preprocessRecs();
    }
    
    public function getNumRecs() {
        return count($this->_records);
    }
    
    public function getRecArray() {
        return $this->_records;
    }
    
    private function _preprocessRecs() {
        
        $numRecs = $this->getNumRecs();
        $pageName = $_SERVER['SERVER_NAME'] .$_SERVER['PHP_SELF'];
        
        for($i=0; $i<$numRecs; $i++) {
            //Build hyperlink to propertie's website
            $this->_records[$i]['url'] = \DBO\Utilities::makeHyperlink($this->_records[$i]['url']);
            
            //Build hyperlink to record detail page
            $id = $this->_records[$i]['id'];
            $url = 'http://' .$pageName ."?action=view&id=" .$id;
            $this->_records[$i]['name'] = \DBO\Utilities::makeHyperlink($url, $this->_records[$i]['name'] );
        }
    }
}
?>
