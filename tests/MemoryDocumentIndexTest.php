<?php namespace Search;


class MemoryDocumentIndexTest extends \PHPUnit_Framework_TestCase {

    public function testMemoryDocumentIndex() {

        $docA = new Document('A', 'a s d', '/a/a');
        $docB = new Document('B', 'b n m', '/b/b');
        $docC = new Document('C', 'c v f', '/c/c');

        $tokenizer = new SimpleTokenizer();
        $index = new MemoryDocumentIndex($tokenizer);
        $index->addDocument($docA);
        $index->addDocument($docB);
        $index->addDocument($docC);

        $this->assertContains($docA, $index->search('a'));
        $this->assertContains($docB, $index->search('b'));
        $this->assertContains($docC, $index->search('c'));

        $this->assertNotContains($docA, $index->search('b'));
        $this->assertNotContains($docA, $index->search('c'));

    }
}
