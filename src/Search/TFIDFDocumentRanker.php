<?php
namespace Search;


/**
 * Class TFIDFDocumentRanker
 * Uses TF-IDF scoring and Cosine Similarity
 * to rank the Documents
 * @package Search
 */
class TFIDFDocumentRanker implements DocumentRanker {

    /**
     * Query after Tokenization
     * @var array
     */
    private $queryTokens;

    /**
     * Query IDF scores
     * @var array
     */
    private $queryTfIdf;

    /**
     * Document after Tokenization
     * @var array
     */
    private $documentTokens;

    /**
     * Frequency of each unique Token in Document
     * @var array
     */
    private $tokenFrequency;

    /**
     *  Number of tokens in document
     * @var integer
     */
    private $documentTokenCount;

    /**
     * Number of documents in index
     */
    private $indexSize;

    /**
     * Init the ranker with the search query
     * @param array $queryTokens Query tokens
     * @param integer $indexSize Number of documents in index
     */
    public function init($queryTokens, $indexSize) {
        $this->queryTokens = $queryTokens;
        $this->queryTfIdf = [];

        $this->indexSize = $indexSize;
    }

    public function cacheTokenFrequency($token, $count) {
        $this->queryTfIdf[$token] = $this->inverseDocumentFrequency($count);
    }

    /**
     * Rank the Document based on TF-IDF scoring and Cosine Similarity
     * @param Document $document Document to rank
     * @return float Document rank
     */
    public function rank(Document $document) {
        $this->initDocument($document->tokens);

        $documentTfIdf = [];

        // Calculate TF-IDF score for each search token
        foreach ($this->queryTokens as $token) {
            $documentTf = $this->termFrequency($token);
            $documentTfIdf[$token] = $documentTf * $this->queryTfIdf[$token];
        }

        return $this->cosineSimilarity($documentTfIdf, $this->queryTfIdf);
    }

    /**
     * Find most important words in given text.
     * @param array $documentTokens Document tokens to find keywords in
     * @return array Result list of keywords ordered by importance.
     */
    public function findKeywords($documentTokens) {
        $this->initDocument($documentTokens);
        $uniqueTokens = array_unique($this->documentTokens);

        $keywords = [];
        foreach($uniqueTokens as $token) {
            $documentTf = $this->termFrequency($token);
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

    private function initDocument($documentTokens) {
        $this->documentTokens = $documentTokens;
        $this->tokenFrequency = array_count_values($this->documentTokens);
        $this->documentTokenCount = count($this->documentTokens);
    }

    /**
     * Calculate the Cosine Similarity between a document and a query string
     * @param array $documentTfIdf Array of TF-IDF scores for the document tokens
     * @param array $queryTfIdf Array of TF-IDF scores for the query tokens
     * @return float Cosine Similarity between document and query
     */
    private function cosineSimilarity($documentTfIdf, $queryTfIdf) {
        $dot = array_sum(array_map(function($a,$b) { return $a*$b; }, $documentTfIdf, $queryTfIdf));
        $absQuery = sqrt(array_sum(array_map(function($a) { return $a*$a; }, $queryTfIdf)));
        $score = $dot / $absQuery;

        return $score;
    }

    /**
     * Calculate the normalized Term Frequency of a Term token in a Content string
     * @param string $term Term to calculate the Term Frequency for
     * @return float The TF score
     */
    private function termFrequency($term) {
        if (isset($this->tokenFrequency[$term])) {
            return $this->tokenFrequency[$term] / $this->documentTokenCount;
        }
        else {
            return 0.0;
        }
    }

    /**
     * Calculate the IDF (Inverse Document Frequency) of a term in the Document Index
     * @param integer $termCount number of documents containing the term
     * @return float The IDF score
     */
    private function inverseDocumentFrequency($termCount) {
        $idf = 1 + log($this->indexSize / ($termCount + 1));
        return $idf*$idf;
    }

}