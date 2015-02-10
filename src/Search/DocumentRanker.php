<?php
namespace Search;

/**
 * Interface DocumentRanker
 * @package Search
 */
interface DocumentRanker {

    /**
     * Init the document ranker with the search query
     * @param array $query Array of query tokens
     * @param integer $indexSize Number of documents in index
     */
    public function init($query, $indexSize);

    /**
     * Init the document ranker with the search query
     * @param string $token Token to cache frequency for
     * @param integer $count Number of documents containing $token
     */
    public function cacheTokenFrequency($token, $count);

    /**
     * Rank a document based on the query
     * @param Document $document Document to rank
     * @return float Rank for the document
     */
    public function rank(Document $document);

    /**
     * Find keywords using the DocumentIndex data
     * @param $content string Content string to find keywords in
     * @return array Array of keywords sorted by their rank
     */
    public function findKeywords($content);

}