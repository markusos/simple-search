<?php namespace Search;

use Search\Config\Config;
use Search\Config\DefaultConfig;

/**
 * Class Engine
 * Main Search Engine class.
 * @package Search
 */
class Engine {

    /**
     * @var Config
     */
    private $config;

    /**
     * Construct a new Search Engine instance
     * @param Config $config  Set the search engine configuration
     */
    public function __construct(Config $config = null)
    {
        if (is_null($config)) {
            $config = Config::createBuilder()->defaultConfig()->build();
        }

        $this->config = $config;
    }

    /**
     * Add a new Document to the search index
     * @param Document $document
     */
    public function addDocument(Document $document) {
        $document->id = $this->size();
        $document->tokens = $this->config->getTokenizer()->tokenize($document->content);

        $this->config->getStore()->addDocument($document);
        $this->config->getIndex()->addDocument($document);
    }

    /**
     * Get the size of the search index
     * @return int number of indexed documents
     */
    public function size() {
        return $this->config->getStore()->size();
    }

    /**
     * Clear the search index of all indexed documents
     * @param string $clear what to clear, default 'all', supports 'store', 'index' and 'all'
     */
    public function clear($clear = 'all') {
        switch($clear) {
            case 'store':
                $this->config->getStore()->clear();
                break;
            case 'index':
                $this->config->getIndex()->clear();
                break;
            case 'all':
                $this->config->getStore()->clear();
                $this->config->getIndex()->clear();
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
        $ranker = $this->config->getRanker();
        $queryTokens = $this->config->getTokenizer()->tokenize($query);

        // Filter stop words
        $queryTokens = array_filter($queryTokens, function($token) {
            return !in_array($token, $this->config->getStopWords());
        });

        // Init the ranker with the query
        $ranker->init($queryTokens, $this->size());

        // Find matching documents
        $documentIds = [];
        foreach ($queryTokens as $token) {
            $result = $this->config->getIndex()->search($token);
            $ranker->cacheTokenFrequency($token, count($result));
            $documentIds += $result;
        }

        // Get matching documents from document store
        $documents = $this->config->getStore()->getDocuments($documentIds);

        // Rank found documents
        foreach ($documents as $document) {
            $document->score = $ranker->rank($document);
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

        $tokenizer = $this->config->getTokenizer();
        $ranker = $this->config->getRanker();

        $tokens = $tokenizer->tokenize($query);
        $ranker->init($tokens, $this->size());

        foreach ($tokens as $token) {
            $result = $this->config->getIndex()->search($token);
            $ranker->cacheTokenFrequency($token, count($result));
        }

        $keywords = $ranker->findKeywords($tokens);

        // If tokens are stemmed, look up original word
        if ($tokenizer instanceof Tokenizer\StemTokenizer) {
            $keywords = array_map(function($token) use ($tokenizer) {
                $token['keyword'] = $tokenizer->getWord($token['keyword']);
                return $token;
            }, $keywords);
        }

        return $keywords;
    }
}