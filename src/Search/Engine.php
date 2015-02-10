<?php namespace Search;

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
        $this->tokenizer = new SimpleTokenizer();

        $this->stopWords = Config::getStopWords();

        if($persistent) {
            $this->index = new MongoDBDocumentIndex($this->tokenizer);
        }
        else {
            $this->index = new MemoryDocumentIndex($this->tokenizer);
        }

        $this->ranker = new TFIDFDocumentRanker();
    }

    /**
     * Add a new Document to the search index
     * @param Document $document
     */
    public function addDocument(Document $document) {
        $this->index->addDocument($document);
    }

    /**
     * Get the size of the search index
     * @return int
     */
    public function size() {
        return $this->index->size();
    }

    /**
     * Clear the search index of all indexed documents
     */
    public function clear() {
        $this->index->clear();
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
        $documents = [];
        foreach ($queryTokens as $token) {
            $result = $this->index->search($token);
            $this->ranker->cacheTokenFrequency($token, count($result));
            $documents += $result;
        }

        // Rank found documents
        foreach ($documents as $document) {
            $document->score = $this->ranker->rank($document);
        }

        // Sort the result according to document rank
        usort($documents, function($a, $b) {
            return $a->score == $b->score ? 0 : ( $a->score > $b->score ) ? -1 : 1;
        } );

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

        return $this->ranker->findKeywords($tokens);
    }
}