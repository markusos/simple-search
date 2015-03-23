<?php namespace Search\Tokenizer;

class PorterTokenizer extends StemTokenizer {

    function __construct() {
        $this->stopWords = [];
    }

    /**
     * Stem all tokens in the input array using the Porter stemming algorithm
     * @param $tokens array of tokens to stem
     * @return array Array of the stemmed tokens
     */
    protected function stem(array $tokens) {
        return array_map(function($token) {
            if (in_array($token, $this->stopWords)) {
                return $token;
            }
            return \Porter::Stem($token);

        }, $tokens);
    }
}