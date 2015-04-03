<?php namespace Search\Config;

use Search\Index\MemoryDocumentIndex;
use Search\Ranker\TFIDFDocumentRanker;
use Search\Store\MemoryDocumentStore;
use Search\Tokenizer\SimpleTokenizer;

class ConfigBuilderTest extends \PHPUnit_Framework_TestCase
{

    private $tokenizer;
    private $index;
    private $store;
    private $ranker;
    private $stopWords;

    function __construct()
    {
        $this->tokenizer = new SimpleTokenizer();
        $this->index = new MemoryDocumentIndex();
        $this->store = new MemoryDocumentStore();
        $this->ranker = new TFIDFDocumentRanker();
        $this->stopWords = ['test'];;
    }

    public function testConfigBuilder()
    {
        $config = Config::createBuilder()
            ->tokenizer($this->tokenizer)
            ->store($this->store)
            ->index($this->index)
            ->ranker($this->ranker)
            ->stopWords($this->stopWords)
            ->build();

        $this->assertEquals($this->tokenizer, $config->getTokenizer());
        $this->assertEquals($this->index, $config->getIndex());
        $this->assertEquals($this->store, $config->getStore());
        $this->assertEquals($this->ranker, $config->getRanker());
        $this->assertEquals($this->stopWords, $config->getStopWords());
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Search Index not defined
     */
    public function testMissingIndex()
    {
        $config = Config::createBuilder()
            ->tokenizer($this->tokenizer)
            ->store($this->store)
            ->ranker($this->ranker)
            ->stopWords($this->stopWords)
            ->build();
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Document Store not defined
     */
    public function testMissingStore()
    {
        $config = Config::createBuilder()
            ->tokenizer($this->tokenizer)
            ->index($this->index)
            ->ranker($this->ranker)
            ->stopWords($this->stopWords)
            ->build();
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Document Tokenizer not defined
     */
    public function testMissingTokenizer()
    {
        $config = Config::createBuilder()
            ->index($this->index)
            ->ranker($this->ranker)
            ->stopWords($this->stopWords)
            ->store($this->store)
            ->build();
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Document Ranker not defined
     */
    public function testMissingRanker()
    {
        $config = Config::createBuilder()
            ->index($this->index)
            ->stopWords($this->stopWords)
            ->store($this->store)
            ->tokenizer($this->tokenizer)
            ->build();
    }
}
