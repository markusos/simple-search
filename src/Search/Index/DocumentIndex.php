<?php namespace Search\Index;

use Search\Document;

/**
 * Interface DocumentIndex
 * @package Search
 */
interface DocumentIndex {

    /**
     * Construct a new DocumentIndex
     */
    public function __construct();

    /**
     * Add a new Document to the DocumentIndex
     * @param Document $document Document to add
     */
    public function addDocument(Document $document);

    /**
     * Search the document index for all documents containing the query token
     * @param $query String token
     * @return array Array of document IDs matching the query
     */
    public function search($query);

    /**
     * Get the number of indexed terms
     * @return integer Number of indexed terms
     */
    public function size();

    /**
     * Clear all indexed documents
     */
    public function clear();
}