<?php namespace Search;

class DocumentTest extends \PHPUnit_Framework_TestCase {

    function testDocument() {

        $doc = new Document('test', 'test document', 'path/to/test/doc');

        $this->assertEquals('test', $doc->getTitle());
        $this->assertEquals('test document', $doc->getContent());
        $this->assertEquals('path/to/test/doc', $doc->getPath());

        $this->setExpectedException('\RuntimeException', 'Trying to access id before indexed');
        $doc->getId();
    }
}
