<?php namespace Search;

use Search\Config\Env;

require_once __DIR__ . '/../../StoreTestTrait.php';

class MongoDocumentStoreTest extends \PHPUnit_Framework_TestCase
{

    use StoreTestTrait;

    function __construct()
    {
        $this->init();
    }

    function init() {
        $this->index = new Index\MongoDBDocumentIndex(Env::get('MONGO_HOST'), Env::get('MONGO_PORT'));
        $this->store = new Store\MongoDBDocumentStore(Env::get('MONGO_HOST'), Env::get('MONGO_PORT'));
    }

    function testConstructor()
    {
        $this->init();

        $this->assertNotEquals(0, $this->index->size());
        $this->assertNotEquals(0, $this->store->size());
    }
}



