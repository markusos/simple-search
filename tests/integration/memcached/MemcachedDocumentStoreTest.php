<?php namespace Search;

require_once __DIR__ . '/../../StoreTestTrait.php';

class MemcachedDocumentTest extends \PHPUnit_Framework_TestCase
{

    use StoreTestTrait;

    function __construct()
    {
        $this->index = new Index\MemcachedDocumentIndex();
        $this->store = new Store\MongoDBDocumentStore();
    }
}



