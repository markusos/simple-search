<?php namespace Search\Index;

use Search\Document;

class MemcachedDocumentIndex implements DocumentIndex {

    /**
     * @var \Memcached
     */
    private $index;

    private $namespace = 'search_';

    private $size;

    /**
     * Construct a new DocumentIndex
     * @param String $host Host setting for Memcached service
     * @param Integer $port Port setting for Memcached service
     */
    public function __construct($host = 'localhost', $port = 11211)
    {
        $this->index = new \Memcached();
        $this->index->addServer($host, $port);
        $this->size = $this->index->get('index_size');
        if ($this->size === false) {
            $this->size = 0;
        }
    }

    /**
     * Add a new Document to the DocumentIndex
     * @param Document $document Document to add
     */
    public function addDocument(Document $document)
    {
        $uniqueTokens = array_unique($document->tokens);
        foreach($uniqueTokens as $token) {

            $documentIds = $this->index->get($this->namespace . $token);
            if ($documentIds === false) {
                $documentIds = [];
                $this->size++;
            }
            $documentIds[] = $document->id;
            $this->index->set($this->namespace . $token, $documentIds);
        }
        $this->index->set('index_size', $this->size);
    }

    /**
     * Search the document index for all documents containing the query token
     * @param $query String token
     * @return array Array of document IDs matching the query
     */
    public function search($query)
    {
        $documentIds = $this->index->get($this->namespace . $query);
        if ($documentIds === false) {
            $documentIds = [];
        }
        return $documentIds;
    }

    /**
     * Get the number of indexed terms
     * @return integer Number of indexed terms
     */
    public function size()
    {
        $this->size = $this->index->get('index_size');
        if ($this->size === false) {
            $this->size = 0;
        }
        return $this->size;
    }

    /**
     * Clear all indexed documents
     */
    public function clear()
    {
        $this->index->flush();
    }
}