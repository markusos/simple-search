<?php namespace Search;

class DocumentTest extends \PHPUnit_Framework_TestCase {

    function testDocument() {

        $doc = new Document('test', 'test document', 'path/to/test/doc');

        $this->assertEquals('test', $doc->title);
        $this->assertEquals('test document', $doc->content);
        $this->assertEquals('path/to/test/doc', $doc->location);
    }
}
