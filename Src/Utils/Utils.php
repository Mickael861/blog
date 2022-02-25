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
}
