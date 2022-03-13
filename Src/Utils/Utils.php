<?php
namespace App\Utils;

use DateTime;

class Utils
{
        
    /**
     * ISO date to FR
     *
     * @param  string $date
     * @return void
     */
    public static function dbToDate(string $date): string
    {
        return (new DateTime($date))->format('d/m/Y');
    }
    
    /**
     * transform special characters into normal characters
     *
     * @param  string $string
     * @return string string treatment
     */
    public static function changeSlugCharacter(string $string): string
    {
        $crct = array('é', 'è', '"', 'ç', 'à', ',', ';', ':', ' ', '\'',
            'ù', 'ê', 'â', 'î', 'ô', 'ä', 'ë', 'ï', 'ö', '&');

        $replace = array('e', 'e',  '', 'c', 'a', '-', '-', '-', '-',  '',
            'u', 'e', 'a', 'i', 'o', 'a', 'e', 'i', 'o', 'et');

        return str_replace($crct, $replace, $string);
    }
}
