<?php

/**
 * This is the bootstrapper for Don! Briggs Objects. It's function is to initialize
 * the environment required for DBO. This script also contains the "Autoloader",
 * which will load classes dynically when they are first called. This prevents
 * having to either load a class manually before you use it, or having to load
 * ALL clases to insure that they are all available.
 * 
 * @name dboinit.php
 * @author Don Briggs <donbriggs@donbriggs.com>
 * @since 20130623
 * @category framework
 * @package Don Briggs Objects (DBO)
 */

    /*
     * Having the database login information here is a security risk, so I moved
     * it to the .htaccess file, where it can not be accessed by the outside world.
     */

    $dbName     = $_SERVER['HTTP_DB_DBNAME'];
    $dbUsername = $_SERVER['HTTP_DB_USERNAME'];
    $dbPassword = $_SERVER['HTTP_DB_PASSWORD'];
    $dbHost     = $_SERVER['HTTP_DB_DBHOST'];
    
    if(empty($_SESSION)) { 
        $_SESSION = array();
        session_start();         
    } 
    
    //--- Store some basic path info in the session
    $_SESSION['docRoot'] = $_SERVER['DOCUMENT_ROOT'];
    $_SESSION['appRoot'] = __DIR__; // This file (dboinit.php) should be in app root
    $_SESSION['libPath'] = $_SESSION['appRoot'] .DIRECTORY_SEPARATOR .'lib';
    $_SESSION['templatePath'] = $_SESSION['appRoot'] .DIRECTORY_SEPARATOR .'templates';
    $_SESSION['includePath'] = $_SESSION['appRoot'] .DIRECTORY_SEPARATOR .'inc';

    /**
     * This is the Autoloader. It allows us to instanciate classes without having
     * to first include the class file. It presumes that the file containing the class to
     * be loaded follows the format: "ClassName.class.php". Note that the class
     * name must be the same as the first part of the filename, or the load will
     * fail.
     * 
     * @param type $className
     * @return void
     */
    function __autoload($className) {
//        print"<pre> Autoloading Class Name: $className \n</pre>";

        $classFile = ($_SESSION['libPath'] .DIRECTORY_SEPARATOR .$className .".class.php");
        $classFile = str_replace("\\", DIRECTORY_SEPARATOR, $classFile);
        $classFile = str_replace("/", DIRECTORY_SEPARATOR, $classFile);
        if(!is_readable($classFile)) {
            throw new \Exception("Class file not readable: " .$classFile);
        }
        if(!file_exists($classFile)) {
            print "<pre>ERROR: Could not find class file.\n";
            print "Class Name: $className \n";
            print "Class File: *$classFile* \n";
            throw new \Exception("Could not find class file.");
        }
        require_once(trim($classFile));
    }

    //Create a new instance of the database class, and store in session for eveybody to use.
    $_SESSION['db'] = new \DBO\MySqlDatabase($dbHost, $dbUsername, $dbPassword, $dbName);
?>
