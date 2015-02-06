<?php
namespace Search;


class TFIDFDocumentRanker implements DocumentRanker {

    /**
     * @var DocumentIndex
     */
    private $index;
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    function __construct(DocumentIndex $index, Tokenizer $tokenizer) {
        $this->tokenizer = $tokenizer;
        $this->index = $index;
    }

    public function rank(Document $document, $query) {
        $documentTfIdf = [];
        $queryTfIdf = [];
        $queryTokens = $this->tokenizer->tokenize($query);

        // Calculate TF-IDF score for each search token
        foreach ($queryTokens as $token) {
            $documentTf = $this->termFrequency($token, $document->content);
            $queryTf = $this->termFrequency($token, $query);
            $tokenIdf = $this->inverseDocumentFrequency($token);

            $documentTfIdf[$token] = $documentTf * $tokenIdf;
            $queryTfIdf[$token] = $queryTf * $tokenIdf;
        }

        return $this->cosineSimilarity($documentTfIdf, $queryTfIdf);
    }

    private function cosineSimilarity($documentTfIdf, $queryTfIdf) {
        $dot = array_sum(array_map(function($a,$b) { return $a*$b; }, $documentTfIdf, $queryTfIdf));
        $absQuery = sqrt(array_sum(array_map(function($a) { return $a*$a; }, $queryTfIdf)));
        $absDocument = sqrt(array_sum(array_map(function($a) { return $a*$a; }, $documentTfIdf)));
        $score = $dot / ($absQuery * $absDocument);

        return $score;
    }

    public function findKeywords($content)  {
        $tokens = array_unique($this->tokenizer->tokenize($content));

        $keywords = [];
        foreach($tokens as $token) {
            $documentTf = $this->termFrequency($token, $content);
            $tokenIdf = $this->inverseDocumentFrequency($token);
            $keywords[$token] = $documentTf * $tokenIdf;
        }

        arsort($keywords);
        $result = [];
        foreach($keywords as $keyword => $score) {
            $result[] = ['keyword' => $keyword, 'score' => $score];
        }

        return $result;
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
        $defaultIDF = 1.5;

        $termDocumentCount = count($this->index->search($term));
        $documentCount = $this->index->size();

        if ($termDocumentCount !== 0) {
            return log($documentCount / ($termDocumentCount));
        }
        else {
            return $defaultIDF;
        }
    }

}