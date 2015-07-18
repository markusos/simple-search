<?php namespace Search;

use Search\Config\Env;
use Search\Document;

require_once __DIR__ . '/../../StoreTestTrait.php';

class SQLDocumentStoreTest extends \PHPUnit_Framework_TestCase
{

    use StoreTestTrait;

    private $pdo;

    function __construct()
    {
        $this->pdo = Env::getPDO();

        // Create the table if it does not exist
        try {
            $statement = $this->pdo->prepare("CREATE TABLE documents (id INT NOT NULL UNIQUE, title VARBINARY (255) NOT NULL, content VARBINARY (2048) NOT NULL);");
            $statement->execute();
        } catch (\Exception $e) {
            // Do nothing!
        }

        $tokenizer = new Tokenizer\PorterTokenizer();
        $this->index = new Index\MemcachedDocumentIndex(Env::get('MEMCACHED_HOST'), Env::get('MEMCACHED_PORT'));
        $this->store = new Store\SQLDocumentStore($this->pdo, $tokenizer);
    }


    function testConstruct()
    {
        $tokenizer = new Tokenizer\PorterTokenizer();
        $this->store = new Store\SQLDocumentStore($this->pdo, $tokenizer);
        $this->assertInstanceOf('\Search\Store\DocumentStore', $this->store);
    }

    function testAddDocument()
    {
        $this->store->clear();

        $tokenizer = new Tokenizer\PorterTokenizer();
        $document = new Document('test', 'this is a test document');
        $document->id = 1;
        $document->tokens = $tokenizer->tokenize($document->content);

        $this->store->addDocument($document);

        $this->assertEquals(1, $this->store->size());

        $d = $this->store->getDocument(1);
        $this->assertEquals($document, $d);

        if (Env::isSQLite()) {
            $this->setExpectedException('Exception', "UNIQUE constraint failed");
        }
        else {
            $this->setExpectedException('Exception', "Duplicate entry '1' for key 'id'");
        }
        $this->store->addDocument($document);

        $this->store->clear();

        $this->assertEquals(0, $this->store->size());
    }
}
