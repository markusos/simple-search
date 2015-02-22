<?php namespace Search;

use Search\Index\DocumentIndex;
use Search\Store\DocumentStore;
use Search\Ranker\DocumentRanker;
use Search\Tokenizer\SnowballTokenizer;
use Search\Tokenizer\Tokenizer;

/**
 * Class Engine
 * Main Search Engine class.
 * @package Search
 */
class Engine {

    /**
     * @var DocumentIndex
     */
    private $index;
    /**
     * @var DocumentStore
     */
    private $store;
    /**
     * @var Tokenizer
     */
    private $tokenizer;
    /**
     * @var DocumentRanker
     */
    private $ranker;

    private $stopWords;

    /**
     * Construct a new Search Engine instance
     * @param bool $persistent
     */
    public function __construct($persistent = true)
    {
        $this->tokenizer = new \Search\Tokenizer\SnowballTokenizer();

        $this->stopWords = array_map(function($word) {
            return $this->tokenizer->tokenize($word)[0];
        }, Config::getStopWords());

        if($persistent) {
            $this->store = new Store\MongoDBDocumentStore();
            $this->index = new Index\MemcachedDocumentIndex();

            // Init index from store data
            if ($this->index->size() === 0 && $this->store->size() > 0) {
                $this->index = $this->store->buildIndex($this->index);
            }
        }
        else {
            $this->index = new Index\MemoryDocumentIndex();
            $this->store = new Store\MemoryDocumentStore();
        }

        $this->ranker = new Ranker\TFIDFDocumentRanker();
    }

    /**
     * Add a new Document to the search index
     * @param Document $document
     */
    public function addDocument(Document $document) {
        $document->id = $this->store->size();
        $document->tokens = $this->tokenizer->tokenize($document->content);

        $this->store->addDocument($document);
        $this->index->addDocument($document);
    }

    /**
     * Get the size of the search index
     * @return int number of indexed documents
     */
    public function size() {
        return $this->store->size();
    }

    /**
     * Clear the search index of all indexed documents
     * @param string $clear what to clear, default 'all', supports 'store', 'index' and 'all'
     */
    public function clear($clear = 'all') {
        switch($clear) {
            case 'store':
                $this->store->clear();
                break;
            case 'index':
                $this->index->clear();
                break;
            case 'all':
                $this->store->clear();
                $this->index->clear();
                break;
        }
    }

    /**
     * Search the search index for matching documents.
     * Result is ranked and ordered by the document ranker class
     * @param string $query The search query used to find matching documents
     * @return array Array of Documents matching the search query, sorted by the ranker class
     */
    public function search($query) {
        $queryTokens = $this->tokenizer->tokenize($query);

        // Filter stop words
        $queryTokens = array_filter($queryTokens, function($token) {
            return !in_array($token, $this->stopWords);
        });

        // Init the ranker with the query
        $this->ranker->init($queryTokens, $this->size());

        // Find matching documents
        $documentIds = [];
        foreach ($queryTokens as $token) {
            $result = $this->index->search($token);
            $this->ranker->cacheTokenFrequency($token, count($result));
            $documentIds += $result;
        }

        // Get matching documents from document store
        $documents = $this->store->getDocuments($documentIds);

        // Rank found documents
        foreach ($documents as $document) {
            $document->score = $this->ranker->rank($document);
        }

        // Sort the result according to document rank
        usort($documents, function($a, $b) {
            return $a->score == $b->score ? 0 : ( $a->score > $b->score ) ? -1 : 1;
        });

        return $documents;
    }

    /**
     * Utils function used to find keywords in a query
     * @param $query string to identify keywords in
     * @return array of keywords, ordered by the ranker class
     */
    public function findKeywords($query) {
        $tokens = $this->tokenizer->tokenize($query);
        $this->ranker->init($tokens, $this->size());

        foreach ($tokens as $token) {
            $result = $this->index->search($token);
            $this->ranker->cacheTokenFrequency($token, count($result));
        }

        $keywords = $this->ranker->findKeywords($tokens);

        // If tokens are stemmed, look up word for
        if ($this->tokenizer instanceof \Search\Tokenizer\SnowballTokenizer) {

            $keywords = array_map(function($token) {
                $token['keyword'] = $this->tokenizer->getWord($token['keyword']);
                return $token;
            }, $keywords);
        }

        return $keywords;
    }
}