<?php namespace Search\Index;

use Search\Document;

class MongoDBDocumentIndex implements DocumentIndex {

    /**
     * @var \MongoClient
     */
    private $connection;

    /**
     * @var \MongoCollection
     */
    private $index;

    function __construct($host='localhost', $port=27017)
    {
        $this->connection = new \MongoClient('mongodb://'. $host .':'. $port);
        $this->index = $this->connection->search->index;
        $this->index->createIndex(array('token' => 1), array('unique' => true));
    }

    public function addDocument(Document $document)
    {
        $uniqueTokens = array_unique($document->tokens);
        foreach($uniqueTokens as $token) {
            $this->index->update(["token" => $token],
                                 ['$push' => ["documents" => $document->id]],
                                 ["upsert" => true]);
        }
    }

    public function search($query)
    {
        $results = [];
        $data = $this->index->findOne(array('token' => $query));

        if(!isset($data)) {
            return $results;
        }

        return $data['documents'];
    }

    public function clear() {
        $this->index->drop();
    }

    /**
     * Get the number of indexed terms
     * @return integer Number of indexed terms
     */
    public function size()
    {
        return $this->index->count();
    }
}