<?php
namespace Search;

/**
 * Interface DocumentRanker
 * @package Search
 */
interface DocumentRanker {

    /**
     * Construct a new DocumentRanker
     * @param DocumentIndex $index DocumentIndex used to rank documents
     * @param Tokenizer $tokenizer Tokenizer used to rank documents
     */
    function __construct(DocumentIndex $index, Tokenizer $tokenizer);

    /**
     * Init the document ranker with the search query
     * @param array $queryTokens Array of query tokens
     */
    public function init($queryTokens);

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