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
        $queryTokens = $this->tokenizer->tokenize($query);

        $documents = [];
        foreach ($queryTokens as $token) {
            $documents += $this->index->search($token);
        }

        foreach ($documents as $document) {
            $document->score = $this->rankDocument($query, $document);
        }

        usort($documents, function($a, $b) {
            return $a->score == $b->score ? 0 : ( $a->score > $b->score ) ? -1 : 1;
        } );

        return $documents;
    }

    private function rankDocument($query, Document $document) {

        $documentTfIdf = [];
        $queryTfIdf = [];
        $queryTokens = $this->tokenizer->tokenize($query);

        // Calculate TF-IDF score for each token
        foreach ($queryTokens as $token) {
            $documentTf = $this->termFrequency($token, $document->getContent());
            $queryTf = $this->termFrequency($token, $query);
            $tokenIdf = $this->inverseDocumentFrequency($token);

            $documentTfIdf[$token] = $documentTf * $tokenIdf;
            $queryTfIdf[$token] = $queryTf * $tokenIdf;
        }

        // Calculate Cosine Similarity
        $dot = array_sum(array_map(function($a,$b) { return $a*$b; }, $documentTfIdf, $queryTfIdf));
        $absQuery = sqrt(array_sum(array_map(function($a) { return $a*$a; }, $queryTfIdf)));
        $absDocument = sqrt(array_sum(array_map(function($a) { return $a*$a; }, $documentTfIdf)));
        $score = $dot / ($absQuery * $absDocument);

        return $score;
    }

    private function termFrequency($term, $content) {
        $tokens = $this->tokenizer->tokenize($content);
        $termCounts = array_count_values($tokens);
        $documentTerms = count($tokens);
        if (isset($termCounts[$term])) {
            return $termCounts[$term] / $documentTerms;
        }
        else {
            return 0;
        }
    }

    private function inverseDocumentFrequency($term) {
        $termDocumentCount = count($this->index->search($term));
        $documentCount = $this->index->size();

            return log($documentCount / ($termDocumentCount));


    }

}