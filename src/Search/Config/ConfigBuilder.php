<?php namespace Search\Config;

use Search\Index\DocumentIndex;
use Search\Ranker\DocumentRanker;
use Search\Store\DocumentStore;
use Search\Tokenizer\Tokenizer;

class ConfigBuilder {

    /**
     * @var \Search\Index\DocumentIndex
     */
    private $index;
    /**
     * @var \Search\Store\DocumentStore
     */
    private $store;
    /**
     * @var \Search\Tokenizer\Tokenizer
     */
    private $tokenizer;
    /**
     * @var \Search\Ranker\DocumentRanker
     */
    private $ranker;

    private $stopWords = [
        "a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", "aren't", "as",
        "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can't", "cannot",
        "could", "couldn't", "did", "didn't", "do", "does", "doesn't", "doing", "don't", "down", "during", "each",
        "few", "for", "from", "further", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", "he", "he'd",
        "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i",
        "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "let's",
        "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", "off", "on", "once", "only", "or",
        "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "shan't", "she", "she'd", "she'll",
        "she's", "should", "shouldn't", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs",
        "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've",
        "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "wasn't", "we", "we'd", "we'll",
        "we're", "we've", "were", "weren't", "what", "what's", "when", "when's", "where", "where's", "which", "while",
        "who", "who's", "whom", "why", "why's", "with", "won't", "would", "wouldn't", "you", "you'd", "you'll",
        "you're", "you've", "your", "yours", "yourself", "yourselves",
    ];

    public function index(DocumentIndex $index) {
        $this->index = $index;
        return $this;
    }

    public function store(DocumentStore $store) {
        $this->store = $store;
        return $this;
    }

    public function tokenizer(Tokenizer $tokenizer) {
        $this->tokenizer = $tokenizer;
        return $this;
    }

    public function ranker(DocumentRanker $ranker) {
        $this->ranker = $ranker;
        return $this;
    }

    public function stopWords(array $stopWords) {
        $this->stopWords = $stopWords;
        return $this;
    }

    public function defaultConfig() {
        $this->tokenizer = new \Search\Tokenizer\PorterTokenizer();
        $this->store = new \Search\Store\MongoDBDocumentStore();
        $this->index = new \Search\Index\MemcachedDocumentIndex();
        $this->ranker = new \Search\Ranker\TFIDFDocumentRanker();

        return $this;
    }

    public function testConfig() {
        $this->tokenizer = new \Search\Tokenizer\SimpleTokenizer();
        $this->index = new \Search\Index\MemoryDocumentIndex();
        $this->store = new \Search\Store\MemoryDocumentStore();
        $this->ranker = new \Search\Ranker\TFIDFDocumentRanker();

        return $this;
    }

    public function build() {
        if(is_null($this->index)) {
            throw new \Exception("Search Index not defined");
        }

        if(is_null($this->store)) {
            throw new \Exception("Document Store not defined");
        }

        if(is_null($this->tokenizer)) {
            throw new \Exception("Document Tokenizer not defined");
        }

        if(is_null($this->ranker)) {
            throw new \Exception("Document Ranker not defined");
        }

        $this->tokenizer->setStopWords($this->stopWords);

        // Init index from store data
        if ($this->index->size() === 0 && $this->store->size() > 0) {
            $this->index = $this->store->buildIndex($this->index);
        }

        return new Config($this);
    }

    public function getStopWords() {
        return $this->stopWords;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getStore() {
        return $this->store;
    }

    public function getTokenizer() {
        return $this->tokenizer;
    }

    public function getRanker() {
        return $this->ranker;
    }
}