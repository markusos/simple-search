<?php namespace Search\Store;

use Search\Document;
use Search\Index\DocumentIndex;
use Search\Tokenizer\Tokenizer;

class SQLDocumentStore implements DocumentStore {

    private $database;
    private $table;
    private $tokenizer;

    function __construct(\PDO $database, Tokenizer $tokenizer, $table = 'documents')
    {
        $this->database = $database;
        $this->table = $table;
        $this->tokenizer = $tokenizer;
    }

    /**
     * Initialize a DocumentIndex with all documents stored in the DocumentStore
     * @param DocumentIndex $index Search index to initialize
     * @return DocumentIndex initialized with documents form the DocumentStore
     */
    public function buildIndex(DocumentIndex $index)
    {
        $query = "SELECT id, title, content FROM " . $this->table;
        $statement = $this->database->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll();

        foreach ($data as $result) {
            $document = $this->buildDocument($result['id'], $result['title'], $result['content']);
            $index->addDocument($document);
        }

        $statement->closeCursor();

        return $index;
    }

    /**
     * Add a new Document to the DocumentStore
     * @param Document $document Document to add
     */
    public function addDocument(Document $document)
    {
        $query = "INSERT INTO ". $this->table ." (id, title, content) VALUES (:id, :title, :content);";
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $document->id);
        $statement->bindParam(':title', $document->title);
        $statement->bindParam(':content', $document->content);
        $statement->execute();
        $statement->closeCursor();
    }

    /**
     * Get a Document from the DocumentStore
     * @param integer $id ID of Document to get
     * @return Document Document matching $id
     */
    public function getDocument($id)
    {
        $query = "SELECT id, title, content FROM ". $this->table ." WHERE id = :id";
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        $data = $statement->fetchObject();

        if ($data) {
            $document = $this->buildDocument($data->id, $data->title, $data->content);
        }
        else {
            $document = null;
        }

        $statement->closeCursor();
        return $document;
    }

    /**
     * Get a Document from the DocumentStore
     * @param array $ids IDs of Documents to get
     * @return array Documents matching $ids
     */
    public function getDocuments($ids)
    {
        $documents = [];

        if ($ids != []) {
            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            $query = "SELECT id, title, content FROM ". $this->table ." WHERE id IN (" . $inQuery . ")";
            $statement = $this->database->prepare($query);
            foreach ($ids as $n => $id) {
                $statement->bindValue(($n+1), $id);
            }
            $statement->execute();
            $data = $statement->fetchAll();

            if ($data) {
                foreach ($data as $result) {
                    $documents[] = $this->buildDocument($result['id'], $result['title'], $result['content']);
                }
            }

            $statement->closeCursor();
        }

        return $documents;
    }

    /**
     * Get the number of stored documents
     * @return integer Number of stored documents
     */
    public function size()
    {
        $query = "SELECT count(*) FROM " . $this->table;
        $statement = $this->database->query($query);
        $statement->bindParam(':tableName', $this->table);
        $size = (int) $statement->fetchColumn();
        $statement->closeCursor();

        return $size;
    }

    /**
     * Clear all stored documents
     */
    public function clear()
    {
        $this->database->exec("DELETE FROM " . $this->table);
    }

    private function buildDocument($id, $title, $content) {
        $document = new Document($title, $content);
        $document->id = $id;
        $document->tokens = $this->tokenizer->tokenize($content);

        return $document;
    }
}