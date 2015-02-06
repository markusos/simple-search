<?php namespace Search;

class MongoDBDocumentIndex implements DocumentIndex {

    private $tokenizer;
    private $connection;
    private $index;
    private $documents;

    function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
        $this->connection = new \MongoClient();
        $this->index = $this->connection->search->index;
        $this->index->createIndex(array('token' => 1));

        $this->documents = $this->connection->search->documents;
    }

    public function addDocument(Document $document)
    {
        $id = new \MongoId();
        $document->_id = $id;
        $document->id = $id;

        $this->documents->insert($document);

        $content = $document->content;
        $tokens = array_unique($this->tokenizer->tokenize($content));
        foreach($tokens as $token) {
            $this->index->update(["token" => utf8_encode($token)],
                                 ['$push' => ["documents" => $id]],
                                 ["upsert" => true]);
        }
    }

    public function search($query)
    {
        $results = [];
        $data = $this->index->findOne(array('token' => utf8_encode($query)));
        foreach($data['documents'] as $documentID) {
            $result = $this->documents->findOne(array('_id' => $documentID));
            $document = new Document($result['title'], $result['content'], $result['location']);
            $document->id = $result['id'];
            $document->_id = $result['_id'];

            $results[] = $document;
        }

        return $results;
    }

    public function size()
    {
        return $this->documents->count();
    }

    public function clear() {
        $this->documents->drop();
        $this->index->drop();
    }
}