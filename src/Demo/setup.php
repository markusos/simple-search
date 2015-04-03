<?php

require '../../vendor/autoload.php';

$pdo = new \PDO('sqlite:documents.sqlite3');
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$tokenizer = new Search\Tokenizer\PorterTokenizer();
$store = new Search\Store\SQLDocumentStore($pdo, $tokenizer);
$index = new Search\Index\MemcachedDocumentIndex();
$ranker = new Search\Ranker\TFIDFDocumentRanker();

$config = Search\Config\Config::createBuilder()
    ->index($index)
    ->store($store)
    ->tokenizer($tokenizer)
    ->ranker($ranker)
    ->build();

$engine = new Search\Engine($config);