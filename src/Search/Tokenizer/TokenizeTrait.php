<?php namespace Search\Tokenizer;

trait TokenizeTrait {

    protected $tokens;
    protected $stemms;
    protected $stopWords;

    protected function toTokens($string) {
        return array_filter(preg_split("/[\\s\\.,?!;:()\\]\\[\\{\\}\\-\\_]+/", strtolower($string)));
    }

    protected function encode($tokens) {
        return array_map(function($token) {
            return utf8_encode($token);
        }, $tokens);
    }

    public function setStopWords(array $stopWords) {
        $this->stopWords = $stopWords;
    }
}