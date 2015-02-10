<?php namespace Search;

class MongoDBDocumentIndex implements DocumentIndex {

    private $tokenizer;
    private $connection;
    private $index;
    private $documents;
    private $size;

    function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
        $this->connection = new \MongoClient();
        $this->index = $this->connection->search->index;
        $this->index->createIndex(array('token' => 1));

        $this->documents = $this->connection->search->documents;
        $this->size = $this->documents->count();
    }

    public function addDocument(Document $document)
    {
        $id = new \MongoId();
        $document->_id = $id;
        $document->id = $this->size;
        $document->tokens = array_map(function($a) {
            return utf8_encode($a);
        }, $this->tokenizer->tokenize($document->content));

        $this->documents->insert($document);

        $uniqueTokens = array_unique($document->tokens);
        foreach($uniqueTokens as $token) {
            $this->index->update(["token" => utf8_encode($token)],
                                 ['$push' => ["documents" => $id]],
                                 ["upsert" => true]);
        }
        $this->size += 1;
    }

    public function search($query)
    {
        $results = [];
        $data = $this->index->findOne(array('token' => utf8_encode($query)));

        if(!isset($data)) {
            return $results;
        }

        foreach($data['documents'] as $documentID) {
            $result = $this->documents->findOne(array('_id' => $documentID));
            $document = new Document($result['title'], $result['content'], $result['location']);
            $document->id = $result['id'];
            $document->tokens = $result['tokens'];
            $document->_id = $result['_id'];

            $results[$document->id] = $document;
        }

        return $results;
    }

    public function size()
    {
        return $this->size;
    }

    public function clear() {
        $this->size = 0;
        $this->documents->drop();
        $this->index->drop();
    }
}