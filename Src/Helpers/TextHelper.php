<?php

namespace Src\Helpers;

class TextHelper 
{
    public static function slugify( string $string,  string $replacement = '-' ) : string
    {
        if(empty($string)) {
            return 'n-a';
        }
    
        $string = preg_replace('~[^\\pL\d]+~u', $replacement, $string);
        $string = trim($string, $replacement);
    
        setlocale(LC_CTYPE, 'en_GB.UTF8');
        $string = iconv('utf-8', 'ASCII//TRANSLIT', $string);
        $string = strtolower($string);
        $string = preg_replace('~[^' . $replacement . '\w]+~', '', $string);
    
        return $string;
    }

}