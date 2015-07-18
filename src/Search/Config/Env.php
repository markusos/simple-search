<?php namespace Search\Config;

class Env
{

    /**
     * Default Environment variables
     * @var array
     */
    private static $envDefaults = [
        'MONGO_HOST' => 'localhost',
        'MONGO_PORT' => '27017',
        'MEMCACHED_HOST' => 'localhost',
        'MEMCACHED_PORT' => '11211',
        'TEST_DATASET_PATH' => 'tests/Wikipedia_sample_dataset.json'
    ];

    private static $pdo = null;
    private static $isSQLite = false;

    /**
     * Get Environment variables or fallback to local defaults if they do not exist;
     * @param $name
     * @return string
     */
    public static function get($name) {
        $env = getenv($name);
        if (!$env) {
            return self::$envDefaults[$name];
        }else {
            return $env;
        }
    }

    /**
     * @return \PDO for connecting either to a local sqlite3 or a mysql database defined by system Env vars
     */
    public static function getPDO() {

        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $db_host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $db_user = getenv('DB_USER');
        $db_pass = getenv('DB_PASSWORD');

        if (!$db_host && !$db_name && !$db_user && !$db_pass) {
            self::$pdo = new \PDO('sqlite:documents.sqlite3');
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$isSQLite = true;
        } else {
            self::$pdo = new \PDO('mysql:host='. $db_host .';dbname='. $db_name .';charset=utf8mb4', $db_user, $db_pass);
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$isSQLite = false;
        }

        return self::$pdo;
    }
    
    public static function isSQLite() {
        return self::$isSQLite;
    }
}
