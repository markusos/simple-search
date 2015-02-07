<?php namespace Search;

/**
 * Interface DocumentIndex
 * @package Search
 */
interface DocumentIndex {

    /**
     * Construct a new DocumentIndex
     * @param Tokenizer $tokenizer Tokenizer used when indexing documents
     */
    public function __construct(Tokenizer $tokenizer);

    /**
     * Add a new Document to the DocumentIndex
     * @param Document $document Document to add
     */
    public function addDocument(Document $document);

    /**
     * Search the document index for all documents containing the query token
     * @param $query String token
     * @return array Array of documents matching the query
     */
    public function search($query);

    /**
     * Get the number of indexed documents
     * @return integer Number of indexed documents
     */
    public function size();

    /**
     * Clear all indexed documents
     */
    public function clear();
}