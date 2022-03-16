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
    
    /**
     * redirect
     *
     * @param  string $url
     * @return void
     */
    public static function redirect(string $url = '/'): void
    {
        header('Location: ' . trim($url));
        exit();
    }

    /**
     * Add errors session
     *
     * @param  string $error
     * @return void
     */
    public static function setErrorsSession(string $error = null): void
    {
        $_SESSION['errors'] = $error;
    }

    /**
     * Add success session
     *
     * @param  string $success
     * @return void
     */
    public static function setSuccessSession(string $success = null): void
    {
        $_SESSION['success'] = $success;
    }
}
