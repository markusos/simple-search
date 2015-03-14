<?php namespace Search;

class DocumentTest extends \PHPUnit_Framework_TestCase {

    function testDocument() {

        $doc = new Document('test', 'test document');

        $this->assertEquals('test', $doc->title);
        $this->assertEquals('test document', $doc->content);
    }
}
