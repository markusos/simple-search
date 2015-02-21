<?php namespace Search;

/**
 * Class SimpleTokenizer
 * @package Search
 */
class SimpleTokenizer implements Tokenizer {
    /**
     * Tokenize the given string
     * Splits the string on space and punctuations
     * @param String $string String to tokenize
     * @return array string tokens
     */
    public function tokenize($string) {
        $tokens = array_filter(preg_split("/[\\s\\.,?!;:()\\]\\[\\{\\}\\-\\_]+/", strtolower($string)));

        $tokens = array_map(function($token) {
            return utf8_encode($token);
        }, $tokens);

        return $tokens;
    }
}