<?php namespace Search;

require_once __DIR__ . '/../../StoreTestTrait.php';

class MongoDocumentStoreTest extends \PHPUnit_Framework_TestCase {

    use StoreTestTrait;

    function __construct(){
        $this->index = new Index\MongoDBDocumentIndex();
        $this->store = new Store\MongoDBDocumentStore();
    }

    function testConstructor() {
        $this->index = new Index\MongoDBDocumentIndex();
        $this->store = new Store\MongoDBDocumentStore();

        $this->assertNotEquals(0, $this->index->size());
        $this->assertNotEquals(0, $this->store->size());
    }
}



