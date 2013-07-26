<?php

namespace Quore;


/**
 * This is the input validator for the Property records. If I had more time I
 * would have preferred to write a Validator class in the DBO namespace, and
 * extend it for spicific objects. -Don!
 */
class PropertyValidator
{
    static function validate(array $recordData) {
        $errors = array();
        
        //--- Validate Name Field
        if(!isset($recordData['name']) || (strlen($recordData['name']) == 0) ) {
            $errors[] = "The 'Property Name' field must be entered";
        }
        if(!self::val_aphanum($recordData['name'])) {
            $errors[] = "The 'Property Name' field must contain alphanumeric data";
        }
        if(!self::val_maxlen($recordData['name'], 50)) {
            $errors[] = "The 'Property Name' field must be 50 characters or less";            
        }
        
        //--- Validate region_id Field
        if(!isset($recordData['region_id']) || (strlen($recordData['region_id']) == 0) ) {
            $errors[] = "The 'Region ID' field must be entered";
        }
        if(!ctype_digit($recordData['region_id'])) {
            $errors[] = "The 'Region Id' field must be numeric data";
        }
        
        //--- Validate Brand Field
        if(!isset($recordData['brand']) || (strlen($recordData['brand']) == 0) ) {
            $errors[] = "The 'Brand' field must be entered";
        }
        if(!self::val_aphanum($recordData['brand'])) {
            $errors[] = "The 'Brand' field must contain alphanumeric data";
        }
        if(!self::val_maxlen($recordData['brand'], 25)) {
            $errors[] = "The 'Brand' field must be 25 characters or less";            
        }
        
        //--- Validate phone Field
        if(!isset($recordData['phone']) || (strlen($recordData['phone']) == 0) ) {
            $errors[] = "The 'Phone' field must be entered";
        }
        if(!self::val_phone($recordData['phone'])) {
            $errors[] = "The 'Phone' field must be in the format xxx-xxx-xxxx";
        }
        if(!self::val_maxlen($recordData['phone'], 25)) {
            $errors[] = "The 'Phone' field must be 25 characters or less";            
        }
        
        //--- Validate isFullService Field
        if(!isset($recordData['isFullService']) || (strlen($recordData['isFullService']) == 0) ) {
            $errors[] = "The 'Service Type' field must be entered";
        }
        if(!self::val_bool($recordData['isFullService'])) {
            $errors[] = "The 'Phone' field must be ia boolean value";
        }
        
        //--- Validate url Field
        if(!isset($recordData['url']) || (strlen($recordData['url']) == 0) ) {
            $errors[] = "The 'Website' field must be entered";
        }
        if(!self::val_url($recordData['url'])) {
            $errors[] = "The 'Website' field must be a valid URL";
        }
        if(!self::val_maxlen($recordData['url'], 255)) {
            $errors[] = "The 'URL' field must be 255 characters or less";            
        }
        
        return $errors;
        
    }
    
    /**
     * Validate that a string contains only Alpha chars and numbners, and spaces
     * 
     * @param string $string
     * @return boolean
     */
    protected static function val_aphanum($string) {
        $string = str_replace(' ', '', $string);
        if(ctype_alnum($string)) {
            return true;
        } else {
            return false;
        }
    }
    
    protected static function val_phone($string) {
        if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $string)) {
            return true;
        } else {
            return false;
        }
    }
    
    protected static function val_bool($string) {
        if($string == 0 || $string == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    protected static function val_url($string) {
        if(filter_var($string, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }
    
    protected static function val_maxlen($string, $maxLen) {
        if(strlen($string) <= $maxLen) {
            return true;
        } else {
            return false;
        }
    }
}
?>
