<?php
namespace components;
class StringUtils {
    
    public static function formatForTextArea($string) {
        return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
    }
    
    public static function formatForHTML($string) {
        return nl2br($string);
    }
    
    public static function generateVerifyKey() {
        $s                = 'jN2GrGxdHYhF83G9EJ6qr38E2J85FtfQ';
        $r                = random_int(6446, 65535);
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $randomString = md5($randomString . $r . $s);
        return $randomString;
    }
    
    public static function truncateString($string, $len) {
        if (strlen($string) > $len) {
            $string = substr($string, 0, $len) . "&hellip;";
        }
        return $string;
    }
    
    
    public static function convertFieldsetToCssClass($str, $replace = array(), $delimiter = '-') {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
            
        }
        
        
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        
        $str = preg_replace("/[^a-zA-Z0-9\/_|+-]$/", '', $str);
        
        // $clean = strtolower(trim($clean, '-'));
        $str = preg_replace("/[\/_|+ -.@;:#~`?]+/", $delimiter, $str);
        
        
        $str = strtolower(trim($str, '-'));
        return $str;
    }
    
    public static function currencyFormat($str) {
        
        return number_format($str, 2, '.', '');
        
    }
    
    public static function surround($s, $char = "'") {
        
        return $char . $s . $char;
        
    }
    
    public static function convertColNameToString($str) {
        return self::convertTableNameToString($str);
    }
    
    public static function convertTableNameToString($str) {
        $label = explode("_", $str);
        
        if (count($label) > 1) {
            $label = array_map('ucfirst', $label);
            $label = implode(" ", $label);
        } else {
            $label = ucfirst($str);
        }
        
        return $label;
        
    }
    
    public static function textToBool($text) {
        
        if (strtolower($text) == "yes")
            $b = 1;
        else
            $b = 0;
        
        return $b;
        
    }
    
    public static function boolToText($text) {
        
        if ($text == "1")
            $b = "Yes";
        else
            $b = "No";
        
        return $b;
        
    }
    
    public static function isSerialized($str) {
        return ($str == serialize(false) || @unserialize($str) !== false);
    }
    
    
    public static function validateUKDate($date, $format = 'd/m/Y') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    public static function convertUKDateToMySql($date) {
        $d         = explode("/", $date);
        $mysqlDate = $d[2] . "-" . $d[1] . "-" . $d[0];
        if (self::validateUKDate($mysqlDate, "Y-m-d")) {
            return $mysqlDate;
        } else
            return false;
    }
    
    public static function intToMonth($num) {
        if ($num == 1)
            return "January";
        if ($num == 2)
            return "February";
        if ($num == 3)
            return "March";
        if ($num == 4)
            return "April";
        if ($num == 5)
            return "May";
        if ($num == 6)
            return "June";
        if ($num == 7)
            return "July";
        if ($num == 8)
            return "August";
        if ($num == 9)
            return "September";
        if ($num == 10)
            return "October";
        if ($num == 11)
            return "November";
        if ($num == 12)
            return "December";
        
        return false;
    }
    
}