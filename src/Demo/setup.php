<?php

require '../../vendor/autoload.php';

function initPDO()
{
    global $pdo;

    $db_host = getenv('DB_HOST');
    $db_name = getenv('DB_NAME');
    $db_user = getenv('DB_USER');
    $db_pass = getenv('DB_PASSWORD');

    if (!$db_host) {
        $pdo = new \PDO('sqlite:documents.sqlite3');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    else {
        $pdo = new \PDO('mysql:host='. $db_host .';dbname='. $db_name .';charset=utf8mb4', $db_user, $db_pass);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}

function setup() {
    global $index;
    global $engine;
    global $store;
    global $pdo;

    $tokenizer = new Search\Tokenizer\PorterTokenizer();
    $store = new Search\Store\SQLDocumentStore($pdo, $tokenizer);

    $memcached_host = getenv('MEMCACHED_HOST');
    $memcached_port = getenv('MEMCACHED_PORT');

    if (!$memcached_host) {
        $index = new Search\Index\MemcachedDocumentIndex();
    } else {
        $index = new Search\Index\MemcachedDocumentIndex($memcached_host, $memcached_port);
    }

    $ranker = new Search\Ranker\TFIDFDocumentRanker();

    $config = Search\Config\Config::createBuilder()
        ->index($index)
        ->store($store)
        ->tokenizer($tokenizer)
        ->ranker($ranker)
        ->build();

    $engine = new Search\Engine($config);
}

