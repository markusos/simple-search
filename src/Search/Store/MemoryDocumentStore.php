<?php namespace Search\Store;

use Search\Document;
use Search\Index\DocumentIndex;

class MemoryDocumentStore implements DocumentStore {

    private $documents;
    private $size;

    function __construct()
    {
        $this->documents = [];
        $this->size = 0;
    }

    /**
     * Initialize a DocumentIndex with all documents stored in the DocumentStore
     * @param DocumentIndex $index Search index to initialize
     * @return DocumentIndex initialized with documents form the DocumentStore
     */
    public function buildIndex(DocumentIndex $index)
    {
        foreach ($this->documents as $document) {
            $index->addDocument($document);
        }

        return $index;
    }

    /**
     * Add a new Document to the DocumentStore
     * @param Document $document Document to add
     */
    public function addDocument(Document $document)
    {
        $this->documents[$document->id] = $document;
        $this->size++;
    }

    /**
     * Get a Document from the DocumentStore
     * @param integer $id ID of Document to get
     * @return Document Document matching $id
     */
    public function getDocument($id)
    {
        if (isset($this->documents[$id])) {
            return $this->documents[$id];
        }
        else {
            return null;
        }
    }

    /**
     * Get a Document from the DocumentStore
     * @param array $ids IDs of Documents to get
     * @return array Documents matching $ids
     */
    public function getDocuments($ids)
    {
        $documents = [];
        foreach ($ids as $id) {
            $document = $this->getDocument($id);
            if ($document !== null) {
                $documents[$id] = $document;
            }
        }

        return $documents;
    }

    /**
     * Get the number of stored documents
     * @return integer Number of stored documents
     */
    public function size()
    {
        return $this->size;
    }

    /**
     * Clear all stored documents
     */
    public function clear()
    {
        $this->size = 0;
        $this->documents = [];
    }
}