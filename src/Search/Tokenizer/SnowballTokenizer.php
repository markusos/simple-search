<?php namespace Search\Tokenizer;

class SnowballTokenizer extends StemTokenizer {

    private $language;

    function __construct($language = 'english') {
        $this->language = $language;
        $this->stopWords = [];
    }

    /**
     * Stem all tokens in the input array
     * @param $tokens array of tokens to stem
     * @return array Array of the stemmed tokens
     */
    protected function stem(array $tokens) {
        $stem = 'stem_' . $this->language;

        return array_map(function($token) use ($stem) {
            if (in_array($token, $this->stopWords)) {
                return $token;
            }
            return $stem($token);

        }, $tokens);
    }
}