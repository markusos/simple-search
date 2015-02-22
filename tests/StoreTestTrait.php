<?php namespace Search;

trait StoreTestTrait {

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
    private $documents;

    public function setUp()
    {
        $this->index->clear();
        $this->store->clear();

        $this->assertEquals(0, $this->index->size());
        $this->assertEquals(0, $this->store->size());

        $tokenizer = new Tokenizer\SimpleTokenizer();

        $this->documents = [
            new Document('A', 'a s d', '/a/a'),
            new Document('B', 'b n m', '/b/b'),
            new Document('C', 'c v f', '/c/c')
        ];

        foreach ($this->documents as $id => $document) {
            $document->id = $id;
            $document->tokens = $tokenizer->tokenize($document->content);
            $this->store->addDocument($document);
        }

        $this->assertEquals(0, $this->index->size());
        $this->index = $this->store->buildIndex($this->index);
    }

    public function tearDown()
    {
        $this->index->clear();
        $this->store->clear();

        $this->assertEquals(0, $this->index->size());
        $this->assertEquals(0, $this->store->size());
    }

    public function testDocumentIndex() {

        $this->assertEquals(9, $this->index->size());

        $this->assertContains($this->documents[0]->id, $this->index->search('a'));
        $this->assertContains($this->documents[1]->id, $this->index->search('b'));
        $this->assertContains($this->documents[2]->id, $this->index->search('c'));

        $this->assertNotContains($this->documents[0]->id, $this->index->search('b'));
        $this->assertNotContains($this->documents[0]->id, $this->index->search('c'));

        $this->assertEquals([], $this->index->search('q'));
    }

    public function testDocumentStore() {

        $this->assertEquals(3, $this->store->size());

        $this->assertEquals($this->documents[0], $this->store->getDocument(0));
        $this->assertEquals($this->documents[1], $this->store->getDocument(1));
        $this->assertEquals($this->documents[2], $this->store->getDocument(2));

        $ids = [0, 1];

        $this->assertContains($this->documents[0], $this->store->getDocuments($ids), '', false, false);
        $this->assertContains($this->documents[1], $this->store->getDocuments($ids), '', false, false);
        $this->assertNotContains($this->documents[2], $this->store->getDocuments($ids), '', false, false);

        $this->assertEquals(null, $this->store->getDocument(99));
        $this->assertEquals([], $this->store->getDocuments([99, 98, 97]));
    }
}