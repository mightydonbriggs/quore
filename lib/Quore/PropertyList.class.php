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
        $sql = "SELECT p.id, p.name, p.brand, r.name AS region, p.phone, p.url \n"
             . "FROM property AS p \n"
             . "LEFT JOIN region AS r \n"
             . "ON p.region_id = r.id \n";
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
        for($i=0; $i<$numRecs; $i++) {
            $this->_records[$i]['url'] = \DBO\Tableizer::makeHyperlink($this->_records[$i]['url']);
        }
    }
}
?>
