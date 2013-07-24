<?php

namespace DBO;  //This is the Namespace for all Don! Briggs Objects

class DBODebug {
    
    public static function pr($var) {
        print "<pre>";
        print_r($var);
        print "</pre>";
    }
    
    public static function pd($var) {
        static::pr($var);
        die("==================================================\n");
    }
}
?>
