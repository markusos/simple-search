<?php namespace Search;

use Search\Config\Env;

require_once __DIR__ . '/../../StoreTestTrait.php';

class MemcachedDocumentTest extends \PHPUnit_Framework_TestCase
{

    use StoreTestTrait;

    function __construct()
    {
        $this->index = new Index\MemcachedDocumentIndex(Env::get('MEMCACHED_HOST'),  Env::get('MEMCACHED_PORT'));
        $this->store = new Store\MongoDBDocumentStore(Env::get('MONGO_HOST'), Env::get('MONGO_PORT'));
    }
}



