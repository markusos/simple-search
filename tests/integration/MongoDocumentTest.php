<?php namespace Search;

require_once __DIR__ . '/../StoreTestTrait.php';

class MongoDocumentTest extends \PHPUnit_Framework_TestCase {

    use StoreTestTrait;

    function __construct(){
        $this->index = new Index\MongoDBDocumentIndex();
        $this->store = new Store\MongoDBDocumentStore();
    }
}



