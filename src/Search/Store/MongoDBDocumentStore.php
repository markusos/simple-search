<?php namespace Search\Store;

use Search\Document;

class MongoDBDocumentStore implements DocumentStore {

    private $connection;
    private $documents;
    private $size;

    function __construct()
    {
        $this->connection = new \MongoClient();
        $this->documents = $this->connection->search->documents;
        $this->size = $this->documents->count();
    }

    /**
     * Add a new Document to the DocumentStore
     * @param Document $document Document to add
     */
    public function addDocument(Document $document)
    {
        $id = new \MongoId();
        $document->_id = $id;
        $this->documents->insert($document);
        unset($document->_id);
        $this->size++;
    }

    /**
     * Get a Document from the DocumentStore
     * @param integer $id ID of Document to get
     * @return Document Document matching $id
     */
    public function getDocument($id)
    {
        $documents = $this->getDocuments([$id]);
        if (isset($documents[$id])) {
            return $documents[$id];
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
        $results = [];
        $documents = $this->documents->find(['id' => ['$in' => $ids ]]);

        foreach ($documents as $result) {
            $document = new Document($result['title'], $result['content'], $result['location']);
            $document->id = $result['id'];
            $document->tokens = $result['tokens'];
            $results[$document->id] = $document;
        }

        return $results;
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
        $this->documents->drop();
    }
}