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

    public function __construct()
    {
        $this->tokenizer = new SimpleTokenizer();
        $this->index = new MemoryDocumentIndex($this->tokenizer);
        $this->ranker = new TFIDFDocumentRanker($this->index, $this->tokenizer);
    }

    public function addDocument(Document $document) {
        $this->index->addDocument($document);
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