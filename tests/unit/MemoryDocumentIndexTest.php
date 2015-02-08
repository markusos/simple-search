<?php namespace Search;


class MemoryDocumentIndexTest extends \PHPUnit_Framework_TestCase {

    public function testMemoryDocumentIndex() {
        $tokenizer = new SimpleTokenizer();
        $index = new MemoryDocumentIndex($tokenizer);

        $docA = new Document('A', 'a s d', '/a/a');
        $docB = new Document('B', 'b n m', '/b/b');
        $docC = new Document('C', 'c v f', '/c/c');

        $index->addDocument($docA);
        $index->addDocument($docB);
        $index->addDocument($docC);

        $this->assertContains($docA, $index->search('a'), '', false, false);
        $this->assertContains($docB, $index->search('b'), '', false, false);
        $this->assertContains($docC, $index->search('c'), '', false, false);

        $this->assertNotContains($docA, $index->search('b'), '', false, false);
        $this->assertNotContains($docA, $index->search('c'), '', false, false);

        $this->assertEquals([], $index->search('q'));
    }
}
