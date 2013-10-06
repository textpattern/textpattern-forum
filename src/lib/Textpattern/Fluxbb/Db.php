<?php

namespace Textpattern\Fluxbb;

/**
 * Database layer.
 */

class Db
{
    /**
     * Stores PDO instance.
     *
     * @var \PDO
     */

    static protected $pdo;

    /**
     * Gets a connection.
     *
     * @return PDO|bool
     * @throws \PDOException
     */

    static public function pdo()
    {
        if (!self::$pdo)
        {
            global $db_host, $db_name, $db_username, $db_password, $db_prefix;
            self::$pdo = new PDO('mysql:dbname='.$db_name.';host='.$db_host, $db_username, $db_password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}
