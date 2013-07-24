<?php

namespace DBO;

class Utilities
{
    public static function makeHyperlink($url, $linkText = null) {
        
        $url = (string) trim($url);
        if(is_null($linkText)) {
            $linkText = $url;
        }
        
        $hyperlink = "<a href='" .$url ."'>" .$linkText ."</a>";
        return $hyperlink;
    }
}

?>
