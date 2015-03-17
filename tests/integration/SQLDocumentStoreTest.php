<?php namespace Search;

use Search\Document;

require_once __DIR__ . '/../StoreTestTrait.php';

class SQLDocumentStoreTest extends \PHPUnit_Framework_TestCase {

    use StoreTestTrait;

    private $pdo;

    function __construct(){
        $this->pdo = new \PDO('sqlite:documents.sqlite3');
        $this->pdo->setAttribute (\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Create the table if it does not exist
        try{
            $statement = $this->pdo->prepare("CREATE TABLE documents (id int NOT NULL UNIQUE, title VARCHAR (255) NOT NULL, content VARCHAR (2048) NOT NULL);");
            $statement->execute();
        }
        catch (\Exception $e) {
            // Do nothing!
        }

        $tokenizer = new Tokenizer\PorterTokenizer();
        $this->index = new Index\MemcachedDocumentIndex();
        $this->store = new Store\SQLDocumentStore($this->pdo, $tokenizer);
    }


    function testConstruct() {
        $tokenizer = new Tokenizer\PorterTokenizer();
        $this->store = new Store\SQLDocumentStore($this->pdo, $tokenizer);
        $this->assertInstanceOf('\Search\Store\DocumentStore', $this->store);
    }

    function testAddDocument() {
        $this->store->clear();

        $tokenizer = new Tokenizer\PorterTokenizer();
        $document = new Document('test', 'this is a test document');
        $document->id = 1;
        $document->tokens = $tokenizer->tokenize($document->content);

        $this->store->addDocument($document);

        $this->assertEquals(1, $this->store->size());

        $d = $this->store->getDocument(1);
        $this->assertEquals($document, $d);

        $this->setExpectedException('Exception', "UNIQUE constraint failed");
        $this->store->addDocument($document);

        $this->store->clear();

        $this->assertEquals(0, $this->store->size());
    }
}
