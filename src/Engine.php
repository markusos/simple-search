<?php namespace Search;

class Engine {

    /**
     * @var DocumentIndex
     */
    private $index;
    /**
     * @var Tokenizer
     */
    private $tokenizer;
    /**
     * @var DocumentRanker
     */
    private $ranker;

    public function __construct($persistent = true)
    {
        $this->tokenizer = new SimpleTokenizer();
        if($persistent) {
            $this->index = new MongoDBDocumentIndex($this->tokenizer);
        }
        else {
            $this->index = new MemoryDocumentIndex($this->tokenizer);
        }
        $this->ranker = new TFIDFDocumentRanker($this->index, $this->tokenizer);
    }

    public function addDocument(Document $document) {
        $this->index->addDocument($document);
    }

    public function size() {
        return $this->index->size();
    }

    public function clear() {
        $this->index->clear();
    }

    public function search($query) {
        $queryTokens = $this->tokenizer->tokenize($query);

        $documents = [];
        foreach ($queryTokens as $token) {
            $documents += $this->index->search($token);
        }

        foreach ($documents as $document) {
            $document->score = $this->ranker->rank($document, $query);
        }

        usort($documents, function($a, $b) {
            return $a->score == $b->score ? 0 : ( $a->score > $b->score ) ? -1 : 1;
        } );

        return $documents;
    }

    public function findKeywords($query) {
        return $this->ranker->findKeywords($query);
    }
}