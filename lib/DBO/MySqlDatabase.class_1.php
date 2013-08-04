<?php

namespace DBO; //Namspace for Don! Briggs Objects


class MySqlDatabase {

    private $_versionDate = '24-Jul-2013';
    
    protected $_dbName;
    protected $_connection;
    public  $_last_query;
    protected $_magic_quotes_active;
    protected $_real_escape_string_exists;
    protected $_logQueries = 0; //Set to 1 to log queries to file
    
    protected static $_errors = array();

    /**
     * Create instance object, login to db server, and connect to db
     *
     * @param string $hostname  Hostname or IP address of database server
     * @param string $dbUsername MySql username
     * @param string $dbPassword MySql password
     * @param string $dbName     Name of database to connect to
     */
    public function __construct($hostname, $dbUsername, $dbPassword, $dbName) {
      $this->open_connection($hostname, $dbUsername, $dbPassword, $dbName);
                  $this->_magic_quotes_active = get_magic_quotes_gpc();
                  $this->_real_escape_string_exists = function_exists( "mysqli_real_escape_string" );
    }

    public function open_connection($hostname, $dbUsername, $dbPassword, $dbName) {
        $this->_connection = mysqli_connect($hostname, $dbUsername, $dbPassword, $dbName);
        if (!$this->_connection) {
                throw new \Exception("Database _connection failed: " . mysqli_error());
        } else {
                $db_select = mysqli_select_db($this->_connection, $dbName);
                if (!$db_select) {
                        throw new \Exception("Database selection failed: " . mysqli_error());
                }
        }
        $this->_dbName = $dbName;
    }

    /**
     * Return the name of the currently connected database
     * 
     * @return string
     */
    public function getDbName() {
        return $this->_dbName;
    }
    
	public function close_connection() {
		if(isset($this->_connection)) {
			mysqli_close($this->_connection);
			unset($this->_connection);
		}
	}

	public function query($sql) {

		$this->_last_query = $sql;
                self::$_errors = array();
                if($this->_logQueries) {
//                    error_log($sql, 3, "/var/log/db.log");
//                    file_put_contents("/tmp/db.log", $sql ."\n\n", FILE_APPEND) ;
                }
                $result = mysqli_query($this->_connection, $sql);
		if($this->confirm_query($result) === false) {
                    return false;
                } else {
                    return $result;
                }
	}

	public function escape_value( $value ) {
		if( $this->_real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysqli_real_escape_string can do the work
			if( $this->_magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysqli_real_escape_string( $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->_magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}

    /**
     * Returns one record as an associative array. Also performs type-casting on fields
     *
     * @param mysqli_result $result_set Result set of a query
     * @return array Array representation of one record
     */
    public function fetch_array($result_set) {
        $rec = mysqli_fetch_assoc($result_set);
        $metas = mysqli_fetch_fields($result_set);
        $rec = $this->_castFields($rec, $metas);
        return $rec;
    }
    
    /**
     * Accepts a query result_set, and returns all records as an array.
     * 
     * @param mysqli_result $result_set
     * @return array Recordset as an array
     */
    public function fetch_array_set($result_set) {
        $array_set = array();
        $numRows = $result_set->num_rows;
        for($i=0; $i<$numRows; $i++) {
            $row = $this->fetch_array($result_set);
            $array_set[$i] = $row;
        }
        return (array) $array_set;
    }

    /**
     * Casts each field in a record array to match the datatype in the corrisponding
     * database field. By default mysqli_fetch_assoc sets all fields to be strings.
     * This function sets them to their proper type.
     *
     * @param array $recArray Array representation or one record
     * @param array $metas Array of mysqli_metadata objects
     * @return array Array representation of one record, with proper field types
     */
    private function _castFields($recArray, $metas) {
        if(!is_array($recArray)) {
            var_dump($recArray);
            throw new \Exception("ERROR: Must pass record array");
        }
        foreach($metas as $meta) {
            $fieldName = $meta->name;
            if(key_exists($fieldName, $recArray)) {
                switch ($meta->type) {
                    case 246:  //Field is a floating point numeric
                        $recArray[$fieldName] = floatval($recArray[$fieldName]);
                        break;
                    case 253:  //Field is a string value
                        $recArray[$fieldName] = strval($recArray[$fieldName]);
                        break;
                    case 3:    //Field is an integer
                        $recArray[$fieldName] = intval($recArray[$fieldName]);
                        break;
                    default:  //Treat as string by default
                        $recArray[$fieldName] = strval($recArray[$fieldName]);
                        break;
                }
            }
        }
        return $recArray;
    }

    /**
     * Get number of rows returned by last query
     * 
     * @param mysqli_result $result_set Result Set object from last query
     * @return int  numRows Number of rows from last query
     */
    public function numRows($result_set) {
        return mysqli_num_rows($result_set);
    }

  public function getInsertId() {
    // get the last id inserted over the current db _connection
    if($insertId =  mysqli_insert_id($this->_connection));
    if($insertId) {
        return $insertId;
    } else {
        return false;
    }
  }

  public function affected_rows() {
    return mysqli_affected_rows($this->_connection);
  }

    private function confirm_query($result) {
        if (!$result) {
            //Query failed
            array_push(self::$_errors, mysqli_error($this->_connection));
            return false;
        } else {
            //Query succeeded
            return true;
        }
    }

    public static function getErrors() {
        return self::$_errors;              
    }
    
    public static function getInstance() {
        if (isset ($_SESSION['db'])) {
            return $_SESSION['db'];
        } else {
            throw new \Exception("ERROR: Could not set Database object from session");
        }
    }
    
    
}

?>
