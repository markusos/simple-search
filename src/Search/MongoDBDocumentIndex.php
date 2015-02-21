<?php namespace Search;

class MongoDBDocumentIndex implements DocumentIndex {

    private $connection;
    private $index;

    function __construct()
    {
        $this->connection = new \MongoClient();
        $this->index = $this->connection->search->index;
        $this->index->createIndex(array('token' => 1));
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