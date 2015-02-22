<?php namespace Search;

require_once __DIR__ . '/../StoreTestTrait.php';

class MemoryDocumentTest extends \PHPUnit_Framework_TestCase {

    use StoreTestTrait;

    function __construct(){
        $this->index = new Index\MemoryDocumentIndex();
        $this->store = new Store\MemoryDocumentStore();
    }
}
