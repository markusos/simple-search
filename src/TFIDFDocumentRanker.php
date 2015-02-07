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

    /**
     * Rank the Document based on TF-IDF scoring and Cosine Similarity
     * @param Document $document Document to rank
     * @param string $query Query string
     * @return float Document rank
     */
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

    /**
     * Calculate the Cosine Similarity between a document and a query string
     * @param array $documentTfIdf Array of TF-IDF scores for the document tokens
     * @param array $queryTfIdf Array of TF-IDF scores for the query tokens
     * @return float Cosine Similarity between document and query
     */
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

    /**
     * Calculate the normalized Term Frequency of a Term token in a Content string
     * @param string $term Term to calculate the Term Frequency for
     * @param string $content Content string
     * @return float The TF score
     */
    private function termFrequency($term, $content) {
        $tokens = $this->tokenizer->tokenize($content);
        $termCounts = array_count_values($tokens);
        $documentTerms = count($tokens);
        if (isset($termCounts[$term])) {
            return $termCounts[$term] / $documentTerms;
        }
        else {
            return 0.0;
        }
    }

    /**
     * Calculate the IDF (Inverse Document Frequency) of a term in the Document Index
     * @param string $term Term to calculate the IDF for
     * @return float The IDF score
     */
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