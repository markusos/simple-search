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

    public function __construct()
    {
        $this->tokenizer = new SimpleTokenizer();
        $this->index = new MemoryDocumentIndex($this->tokenizer);
    }

    public function addDocument(Document $document) {
        $this->index->addDocument($document);
    }

    public function search($query) {
        $documents = $this->index->search($query);

        foreach ($documents as $document) {
            $document->score = $this->termFrequency($query, $document) * $this->inverseDocumentFrequency($query);
        }

        usort( $documents, function($a, $b) {
            return $a->score == $b->score ? 0 : ( $a->score > $b->score ) ? -1 : 1;
        } );

        return $documents;
    }

    public function termFrequency($term, Document $document) {
        $tokens = $this->tokenizer->tokenize($document->getContent());
        $counts = array_count_values($tokens);
        if (isset($counts[$term])) {
            return $counts[$term];
        }
        else {
            return 0;
        }
    }

    public function inverseDocumentFrequency($term) {
        $termDocumentCount = count($this->index->search($term));
        $documentCount = $this->index->size();

            return log($documentCount / ($termDocumentCount));


    }

}