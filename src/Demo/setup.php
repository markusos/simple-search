<?php

require '../../vendor/autoload.php';
use Search\Engine;
use Search\Config\Env;
use Search\Config\Config;
use Search\Store\SQLDocumentStore;
use Search\Tokenizer\PorterTokenizer;
use Search\Store\MongoDBDocumentStore;
use Search\Ranker\TFIDFDocumentRanker;
use Search\Index\MemcachedDocumentIndex;

function setup() {
    global $index;
    global $engine;
    global $store;

    $tokenizer = new PorterTokenizer();
    $store = new SQLDocumentStore(Env::getPDO(), $tokenizer);
    //$store = new MongoDBDocumentStore(ENV::get('MONGO_HOST'), ENV::get('MONGO_PORT'));
    $index = new MemcachedDocumentIndex(ENV::get('MEMCACHED_HOST'), ENV::get('MEMCACHED_PORT'));
    $ranker = new TFIDFDocumentRanker();

    $config = Config::createBuilder()
        ->index($index)
        ->store($store)
        ->tokenizer($tokenizer)
        ->ranker($ranker)
        ->build();

    $engine = new Engine($config);
}

