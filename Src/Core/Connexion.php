<?php
namespace App\Core;

use Exception;
use PDO;

class Connexion extends PDO
{
    private static $instance;

    private const DBNAME = 'blog';
    private const DBHOST = '127.0.0.1:3306';
    private const DBUSER = 'root';
    private const DBPASS = '';

    public function __construct()
    {
        $dsn = 'mysql:dbname=' . self::DBNAME . ';host=' . self::DBHOST . '\'';

        try {
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            parent::__construct($dsn, self::DBUSER, self::DBPASS, $options);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }
    
    /**
     * PDO instance recovery
     *
     * @return PDO
     */
    public function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
