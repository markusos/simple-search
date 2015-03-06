<?php namespace Search\Config;


use Search\Index\DocumentIndex;

class Config {

    /**
     * @var \Search\Index\DocumentIndex
     */
    protected $index;
    /**
     * @var \Search\Store\DocumentStore
     */
    protected $store;
    /**
     * @var \Search\Tokenizer\Tokenizer
     */
    protected $tokenizer;
    /**
     * @var \Search\Ranker\DocumentRanker
     */
    protected $ranker;

    protected $stopWords;

    public function __construct(ConfigBuilder $builder) {
        $this->stopWords = $builder->getStopWords();
        $this->index = $builder->getIndex();
        $this->store = $builder->getStore();
        $this->tokenizer = $builder->getTokenizer();
        $this->ranker = $builder->getRanker();
    }

    public static function createBuilder() {
        return new ConfigBuilder();
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