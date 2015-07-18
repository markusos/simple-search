<?php

require '../../vendor/autoload.php';
use Search\Config\Env;

function setup() {
    global $index;
    global $engine;
    global $store;

    $tokenizer = new Search\Tokenizer\PorterTokenizer();
    $store = new Search\Store\SQLDocumentStore(Env::getPDO(), $tokenizer);
    //$store = new Search\Store\MongoDBDocumentStore(ENV::get('MONGO_HOST'), ENV::get('MONGO_PORT'));
    $index = new Search\Index\MemcachedDocumentIndex(ENV::get('MEMCACHED_HOST'), ENV::get('MEMCACHED_PORT'));
    $ranker = new Search\Ranker\TFIDFDocumentRanker();

    $config = Search\Config\Config::createBuilder()
        ->index($index)
        ->store($store)
        ->tokenizer($tokenizer)
        ->ranker($ranker)
        ->build();

    $engine = new Search\Engine($config);
}

