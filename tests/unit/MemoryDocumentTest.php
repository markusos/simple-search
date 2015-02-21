<?php namespace Search;


class MemoryDocumentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Index\DocumentIndex
     */
    private $index;
    /**
     * @var Store\DocumentStore
     */
    private $store;

    /**
     * @var Document
     */
    private $docA, $docB, $docC;

    public function setUp()
    {
        $tokenizer = new SimpleTokenizer();
        $this->index = new Index\MemoryDocumentIndex();
        $this->store = new Store\MemoryDocumentStore();

        $this->docA = new Document('A', 'a s d', '/a/a');
        $this->docB = new Document('B', 'b n m', '/b/b');
        $this->docC = new Document('C', 'c v f', '/c/c');

        $this->docA->id = 0;
        $this->docB->id = 1;
        $this->docC->id = 2;

        $this->docA->tokens = $tokenizer->tokenize($this->docA->content);
        $this->docB->tokens = $tokenizer->tokenize($this->docB->content);
        $this->docC->tokens = $tokenizer->tokenize($this->docC->content);

        $this->index->addDocument($this->docA);
        $this->index->addDocument($this->docB);
        $this->index->addDocument($this->docC);

        $this->store->addDocument($this->docA);
        $this->store->addDocument($this->docB);
        $this->store->addDocument($this->docC);
    }

    public function tearDown()
    {
        $this->index->clear();
        $this->store->clear();
        $this->assertEquals(0, $this->store->size());
        $this->assertEquals(0, $this->index->size());
    }

    public function testDocumentIndex() {

        $this->assertEquals(9, $this->index->size());

        $this->assertContains($this->docA->id, $this->index->search('a'));
        $this->assertContains($this->docB->id, $this->index->search('b'));
        $this->assertContains($this->docC->id, $this->index->search('c'));

        $this->assertNotContains($this->docA->id, $this->index->search('b'));
        $this->assertNotContains($this->docA->id, $this->index->search('c'));

        $this->assertEquals([], $this->index->search('q'));
    }

    public function testDocumentStore() {

        $this->assertEquals(3, $this->store->size());

        $this->assertEquals($this->docA, $this->store->getDocument($this->docA->id));
        $this->assertEquals($this->docB, $this->store->getDocument($this->docB->id));
        $this->assertEquals($this->docC, $this->store->getDocument($this->docC->id));

        $ids = [$this->docA->id, $this->docB->id];

        $this->assertContains($this->docA, $this->store->getDocuments($ids));
        $this->assertContains($this->docB, $this->store->getDocuments($ids));
        $this->assertNotContains($this->docC, $this->store->getDocuments($ids));

        $this->assertEquals(null, $this->store->getDocument(99));
        $this->assertEquals([], $this->store->getDocuments([99, 98, 97]));
    }
}
