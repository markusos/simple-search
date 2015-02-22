<?php namespace Search\Store;

use Search\Document;
use Search\Index\DocumentIndex;

/**
 * Interface DocumentStore
 * @package Search
 */
interface DocumentStore {

    /**
     * Initialize a DocumentIndex with all documents stored in the DocumentStore
     * @param DocumentIndex $index Search index to initialize
     * @return DocumentIndex initialized with documents form the DocumentStore
     */
    public function buildIndex(DocumentIndex $index);

    /**
     * Add a new Document to the DocumentStore
     * @param Document $document Document to add
     */
    public function addDocument(Document $document);

    /**
     * Get a Document from the DocumentStore
     * @param integer $id ID of Document to get
     * @return Document Document matching $id
     */
    public function getDocument($id);

    /**
     * Get a Document from the DocumentStore
     * @param array $ids IDs of Documents to get
     * @return array Documents matching $ids
     */
    public function getDocuments($ids);

    /**
     * Get the number of stored documents
     * @return integer Number of stored documents
     */
    public function size();

    /**
     * Clear all stored documents
     */
    public function clear();

}