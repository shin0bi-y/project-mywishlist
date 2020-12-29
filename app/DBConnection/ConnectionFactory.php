<?php

namespace mywishlist\bd;

use PDO;
use PDOException;

class ConnectionFactory
{

    //creds
    private static $user = "root";
    private static $pass = "";
    private static $dbName = "mywishlist";
    static $db;

    public static function setConfig($file)
    {
        $vars = parse_ini_file($file);
        self::$user = $vars["user"];
        self::$pass = $vars["pass"];
        self::$dbName = $vars["dbName"];
    }

    public static function makeConnection()
    {
        try {
            $dsn = "mysql:host=localhost;dbname=".self::$dbName;

            self::$db = new PDO($dsn, self::$user, self::$pass, array(PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false));

            echo "connected";
        } catch (PDOException $e) {
            die("connection: $dsn " . $e->getMessage() . '<br>');
        }
    }
}