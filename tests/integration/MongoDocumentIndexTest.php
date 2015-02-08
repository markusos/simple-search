<?php namespace Search;

class MongoDocumentIndexTest extends \PHPUnit_Framework_TestCase {


    public function testMongoDBDocumentIndex() {
        $tokenizer = new SimpleTokenizer();
        $index = new MongoDBDocumentIndex($tokenizer);

        $index->clear();
        $this->assertEquals(0, $index->size());

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

        $index->clear();
        $this->assertEquals(0, $index->size());
    }
}


